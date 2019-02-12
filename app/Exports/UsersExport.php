<?php
declare(strict_types = 1);

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $id;

    public function __construct(array $id)
    {
        $this->id = $id;
    }


    /**
     * @return Builder
     */
    public function query()
    {
        return User::query()->whereIn('id', $this->id);
    }


    /**
     * @return array
     */
    public function headings(): array
    {
        return [ 'ID', 'First name', 'Last name', 'Email', 'Role' ];
    }


    /**
     * @param mixed $user
     * @return array
     */
    public function map($user): array
    {
        return [
            $user->id,
            $user->fname,
            $user->lname,
            $user->email ?? null,
            $user->getRoleName->name ?? null,
        ];
    }
}
