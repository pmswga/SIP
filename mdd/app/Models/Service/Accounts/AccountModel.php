<?php

namespace App;

use App\Core\Constants\ListSubSystemConstants;
use App\Models\Main\Staff\EmployeeModel;
use App\Models\Service\Accounts\AccountRightsModel;
use App\Models\Service\Accounts\ListAccountTypeModel;
use App\Models\Service\Lists\ListSubSystemModel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class AccountModel extends Authenticatable
{
    use Notifiable;

    protected $table = "Accounts";
    protected $primaryKey = "idAccount";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'idEmployee', 'idAccountType'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getEmail() {
        return $this->email;
    }

    public function getEmployee() {
        return $this->hasOne('App\Models\Main\Staff\EmployeeModel', 'idEmployee', 'idEmployee')->first();
    }

    public function getAccountType() {
        return $this->hasOne('App\Models\Service\Accounts\ListAccountTypeModel', 'idAccountType', 'idAccountType')->first();
    }

    public function getIdAccountType() {
        return $this->idAccountType;
    }

    public function getAccountRights() {
        $rights = $this->hasMany(AccountRightsModel::class, 'idAccount', 'idAccount')
            ->where('isAccess', '=', 1)
            ->whereNotIn('idSubSystem', [
                ListSubSystemConstants::Storage,
                ListSubSystemConstants::Tickets
            ])
            ->get();

        $groupRights = [];
        foreach ($rights as $right) {
            $groupRights[$right->getSubSystem()->getSystemSection()->getCaption()][] = $right;
        }

        return $groupRights;
    }

    public function getAccountRightsOn(int $systemId) {
        return $this->hasOne(AccountRightsModel::class, 'idAccount', 'idAccount')
            ->where('idSubSystem', '=', $systemId)
            ->first();
    }

}
