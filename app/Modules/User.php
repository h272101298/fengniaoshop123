<?php
/**
 * Created by PhpStorm.
 * User: zeng
 * Date: 2018/6/21
 * Time: 下午2:32
 */

namespace App\Modules;


use App\Modules\Advert\AdvertHandle;
use App\Modules\Order\OrderHandle;
use App\Modules\Product\ProductHandle;
use App\Modules\Proxy\ProxyHandle;
use App\Modules\Role\Model\Role;
use App\Modules\Role\Model\RoleUser;
use App\Modules\Role\RoleHandle;
use App\Modules\SettleApply\SettleApplyHandle;
use App\Modules\Store\StoreHandle;
use App\Modules\System\SystemHandle;
use App\Modules\WeChatUser\Model\UserAmount;

class User
{
    use AdvertHandle;
    use StoreHandle;
    use RoleHandle;
    use SettleApplyHandle;
    use ProductHandle;
    use SystemHandle;
    use OrderHandle;
    use ProxyHandle;
    public function addUser($id,$data,$role)
    {
        if ($id){
            $user = \App\User::find($id);
        }else{
            $user = new \App\User();
        }
        foreach ($data as $key=>$value){
            $user->$key = $value;
        }
        if ($user->save()){
            if ($role){
                $roleUser = RoleUser::where('user_id','=',$user->id)->first();
                if (empty($roleUser)){
                    $roleUser = new RoleUser();
                }
                $roleUser->user_id = $user->id;
                $roleUser->role_id = $role;
                $roleUser->save();
            }
            return true;
        }
        return false;
    }
    public function getUsers($page,$limit)
    {
        $count = \App\User::count();
        $data = \App\User::limit($limit)->offset(($page-1)*$limit)->get();
        if (!empty($data)){
            foreach ($data as $user) {
                $id = RoleUser::where('user_id','=',$user->id)->pluck('role_id')->first();
                $user->role = $id?Role::find($id)->display_name:'';
            }
        }
        return [
            'count'=>$count,
            'data'=>$data
        ];
    }
    public function addUserAmount($user_id,$price)
    {
        $amount = UserAmount::where('user_id','=',$user_id)->first();
        if (empty($amount)){
            $amount = new UserAmount();
            $amount->user_id = $user_id;
            $amount->amount = 0;
        }
        $amount->amount += $price;
        if ($amount->save()){
            return true;
        }
        return false;
    }


}