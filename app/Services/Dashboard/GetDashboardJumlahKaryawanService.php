<?php
namespace App\Services\Dashboard;

use App\Models\Role;
use App\Services\DefaultService;
use App\Services\ServiceInterface;
use Illuminate\Support\Facades\DB;

class GetDashboardJumlahKaryawanService extends DefaultService implements ServiceInterface
{
    public function process($dto) 
    {
        //Jumlah Karyawan
        $roles = Role::where('deleted_at',null)
        ->where('id', '!=', 1)
        ->orderBy('id', 'asc')
        ->select('id', 'name')->get();
        
        $rolesCount = [];
        foreach ($roles as $role) {
            $rolesCount[$role->name] = 0;
        }

        $employees = DB::select("
            SELECT ur.role_id, COUNT(u.id) as total
            FROM users u
            INNER JOIN user_roles ur ON u.id = ur.user_id
            WHERE ur.role_id != 1
            AND u.deleted_at IS NULL
            GROUP BY ur.role_id
        ");

        $totalEmployees = 0;

        foreach ($employees as $row) {
            $totalEmployees += $row->total;

            $role = $roles->where('id', $row->role_id)->first();
            if ($role) {
                $rolesCount[$role->name] = $row->total;
            }
        }

        $header = 
        [
            'jumlah_karyawan' => $totalEmployees,
            'roles' => $rolesCount
        ];

        return $this->results['data'] = $header;
    }
}