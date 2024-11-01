<?php

namespace App\Models;

use App\Providers\UtilityServiceProvider as u;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    public $timestamps = false;

    public function getRoleDetail($id)
    {
        $role = Role::query()->where('id', "=", (int)$id)->first();
        $roles = self::getAllRoles();
        $modules = self::getAllModules();
        return ['role' => $role, 'roles' => $roles, 'modules' => $modules];
    }

    private function getAllRoles()
    {
        $query = "select * from config where `key` LIKE 'role%'";
        return u::query($query);
    }

    public function getAllModules()
    {
        $query = "select * from config where `key`  regexp 'mod[0-9]{1,2}\..*'";
        return u::query($query);
    }

    /**
     * @param $functions [1.1:1, 1.2.2, ...]
     * @param $module "1.1"
     * @return string | null "1.1:1"
     */
    private function getModuleConfig($functions, $module)
    {
        if (empty($functions)) return null;

        foreach ($functions as $func) {
            if (explode(":", $func)[0] === $module) {
                return $func;
            }
        }
        return null;
    }

    private function generateModuleConfig($currentModuleConfig, $module, $role, $status)
    {
        $res = $currentModuleConfig ?: "$module:";
        $arr = explode(':', $res);
        $actions = $arr[1] == null || $arr[1] == "" ? [] : explode(".", $arr[1]);

        if ($status) {
            array_push($actions, $role);
        } else {
            $actions = array_diff($actions, [$role]);
        }
        $actions = array_values(array_sort($actions));
        return "$module:" . implode(".", $actions);
    }

    private function addModuleConfigToFunctions($functions, $moduleConfig, $moduleName)
    {
        foreach ($functions as $key => $item) {
            if (explode(":", $item)[0] !== $moduleName) {
                continue;
            }
            if (explode(":", $moduleConfig)[1] == "") {
                unset($functions[$key]);
//                dd($functions);
            } else {
                $functions[$key] = $moduleConfig;
            }
            $functions = array_values($functions);
            return $functions;

        }
        $functions[] = $moduleConfig;
        $functions = array_values(array_sort($functions));
        return $functions;
    }

    public function updateRole($id, $params)
    {
        $role = Role::query()->where('id', "=", (int)$id)->first();
        $functions = json_decode($role->functions);
        $module = $params['module'];
        $status = $params['status'];
        $role = $params['role'];
        $moduleConfig = self::getModuleConfig($functions, $module);
        $moduleConfig = self::generateModuleConfig($moduleConfig, $module, $role, $status);
        $functions = self::addModuleConfigToFunctions($functions, $moduleConfig, $module);
        Role::where('id', $id)
            ->update(['functions' => json_encode($functions)]);
    }

    public function updateRoleStatus($id, $status)
    {
        Role::where('id', $id)
            ->update(['status' => $status ? 1 : 0]);
    }
}
