<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                return [
                    'name' => 'required|between:3,25|regex:/^[A-Za-z0-9\-\_]+$/|unique:users,name,' . Auth::id(),
                    'email' => 'required|email',
                    'introduction' => 'max:80',
                    'avatar' => 'mimes:jpeg,bmp,png,gif|dimensions:min_width=208,min_height=208',
                ];
                break;
            case 'PATCH':
                $userId = \Auth::guard('api')->id();
                return [
                    'name' => 'between:3,25|regex:/^[A-Za-z0-9\-\_]+$/|unique:users,name,' .$userId,
                    'email'=>'email|unique:users,email,'.$userId,
                    'introduction' => 'max:80',
                    'avatar_image_id' => 'exists:images,id,type,avatar,user_id,'.$userId,
                ];
                break;
            default:
                # code...
                break;
        }
    }

    public function attributes()
    {
        return [
            'verification_key'=>'短信验证码 key',
            'verification_code'=>'短信验证码',
        ];
    }
}
