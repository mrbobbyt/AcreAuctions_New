<?php
declare(strict_types=1);

namespace App\Services\Seller;

use App\Mail\SellerCreateMail;
use App\Models\Email;
use App\Models\Image;
use App\Models\RegisterToken;
use App\Models\Seller;
use App\Models\Telephone;
use App\Models\User;
use App\Services\Auth\Contracts\UserAuthServiceContract;
use App\Services\Seller\Exceptions\NoHaveRegisterToken;
use Illuminate\Database\Eloquent\Model;
use Mail;

use App\Repositories\User\Contracts\UserRepositoryContract;
use App\Services\Image\Contracts\AvatarServiceContract;
use App\Services\Telephone\Contracts\TelServiceContract;
use App\Repositories\Seller\Contracts\SellerRepositoryContract;
use App\Services\Seller\Contracts\SellerServiceContract;

use Throwable;
use Exception;
use App\Services\Seller\Exceptions\SellerAlreadyExistsException;

class SellerService implements SellerServiceContract
{
    protected $model;
    protected $sellerRepo;
    protected $userRepo;
    protected $userService;
    protected $telService;
    protected $avatarService;

    public function __construct(
        Seller $seller,
        SellerRepositoryContract $sellerRepo,
        UserRepositoryContract $userRepo,
        UserAuthServiceContract $userService,
        TelServiceContract $telService,
        AvatarServiceContract $avatarService
    )
    {
        $this->model = $seller;
        $this->sellerRepo = $sellerRepo;
        $this->userRepo = $userRepo;
        $this->userService = $userService;
        $this->telService = $telService;
        $this->avatarService = $avatarService;
    }


    /**
     * Create new seller
     * @param array $data
     * @return Model
     * @throws Throwable
     */
    public function create(array $data)
    {
        $userData = [
            'fname' => $data['f_name'],
            'lname' => $data['l_name'],
            'email' => $data['email'],
            'role' => User::ROLE_SELLER,
        ];

        $user = $this->userService->create($userData);
        $registerToken = $this->userService->createToken($user->email);
        $this->userService->createRegisterToken($user->email, $registerToken);

        $sellerTitle = $data['company'] ?? $data['f_name'] . '-' . $data['l_name'];
        $sellerData = [
            'user_id' => $user->id,
            'title' => $sellerTitle,
            'slug' => make_url($sellerTitle),
            'is_verified' => 0,
            'address' => $data['mail_address'],
        ];

        $seller = $this->model->query()->make()->fill($sellerData);
        $seller->saveOrFail();

        $this->telService->create(
            Telephone::TYPE_SELLER,
            (int)$data['phone_number'],
            $seller->id
        );

        $this->sendAuthMail($data['clientUrl'], $data['email'], $registerToken);

        return $seller;
    }


    /**
     * Update seller
     * @param array $data
     * @param int $id
     * @return Model
     * @throws SellerAlreadyExistsException
     * @throws Throwable
     */
    public function update(array $data, int $id): Model
    {
        $seller = $this->sellerRepo->findByPk($id);

        if ($data['image']) {
            $this->avatarService->update($data['image']['image'], $id, Image::TYPE_SELLER_LOGO);
        }

        if ($data['email']) {
            foreach ($data['email']['email'] as $key => $item) {
                $this->updateEmail($key, $item, $id);
            }
        }
        if ($data['telephones']) {
            foreach ($data['telephones']['telephones'] as $key => $item) {
                $this->telService->update($key, $item, $id);
            }
        }

        if ($data['body']) {
            if (isset($data['body']['title']) && $data['body']['title']) {
                if ($this->sellerRepo->findByTitle($data['body']['title'])) {
                    throw new SellerAlreadyExistsException();
                }
                $data['body']['slug'] = make_url($data['body']['title']);
            }

            foreach ($data['body'] as $key => $property) {
                $seller->$key = $property;
            }

            $seller->saveOrFail();
        }

        return $seller;
    }


    /**
     * Delete seller
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function delete(int $id): bool
    {
        $seller = $this->sellerRepo->findByPk($id);
        $this->avatarService->delete($seller->avatar);
        $this->deleteEmails($seller);
        $this->telService->delete($seller);
        $seller->delete();

        return true;
    }


    /**
     * Save emails
     * @param string $email
     * @param int $id
     * @return bool
     * @throws Throwable
     */
    protected function createEmail(string $email, int $id)
    {
        $model = Email::query()->make()->fill([
            'entity_id' => $id,
            'entity_type' => Email::TYPE_SELLER,
            'email' => $email,
        ]);

        return $model->saveOrFail();
    }


    /**
     * @param int $key
     * @param string $item
     * @param int $id
     * @return bool
     * @throws Throwable
     */
    protected function updateEmail(int $key, string $item, int $id): bool
    {
        if ($email = $this->sellerRepo->findEmail($key, $id)) {
            $email->email = $item;
            return $email->saveOrFail();
        }

        return $this->createEmail($item, $id);
    }


    /**
     * Delete all related emails
     * @param Model $seller
     * @return mixed
     */
    protected function deleteEmails(Model $seller)
    {
        return $seller->emails->each(function ($item, $key) {
            $item->delete();
        });
    }

    /**
     * @param string $clientUrl
     * @param string $email
     * @param string $token
     * @return mixed|void
     */
    public function sendAuthMail(string $clientUrl, string $email, string $token)
    {
        $mail = new SellerCreateMail($clientUrl, $token);
        Mail::to($email)->send($mail);
    }

    /**
     * @param array $data
     * @return Model
     * @throws NoHaveRegisterToken
     */
    public function authSeller(array $data): Model
    {
        $registerToken = RegisterToken::query()->where('token', $data['token'])->first();

        if (!$registerToken) {
            throw new NoHaveRegisterToken();
        }

        if ($user = $this->userRepo->findByEmail($registerToken->email)) {
            $registerToken->delete();
        }

        $user->password = bcrypt($data['password']);
        $user->email_verified_at = date('Y-m-d H:i:s');
        $user->save();

        return $user;
    }
}
