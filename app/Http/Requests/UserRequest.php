<?php

namespace App\Http\Requests;

use App\Models\Content;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    protected function getValidatorInstance()
    {
        return parent::getValidatorInstance()->after(function ($validator) {
            // Call the after method of the FormRequest (see below)
            $this->after($validator);
        });
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name' => 'required|max:255',
        ];

        if ($this->route('user')) {
            $optionalRules = [
                'email' => 'required|email|max:255|unique:users,email,' . $this->route('user'),
            ];
        } else {
            $optionalRules = [
                'email' => 'required|email|max:255|unique:users',
            ];
        }

        return array_merge($rules, $optionalRules);
    }

    public function after($validator)
    {
      /*  if ((!in_array(1, $this->roles)) && (! $this->filled('department_id'))) {
            $validator->errors()->add('department_id.required', 'Vui lòng chọn phòng ban');
        }

        if ((!in_array(1, $this->roles)) && (! $this->filled('contents'))) {
            $validator->errors()->add('contents.required', 'Vui lòng chọn ít nhất một tài khoản quảng cáo');
        }*/
    }

    public function messages()
    {
        return [
            'name.required' => 'Vui lòng không để trống tên người dùng',
            'email.required' => 'Vui lòng không để trống email',
            'department_id.required' => 'Vui lòng chọn phòng ban',
            'contents.required' => 'Vui lòng chọn ít nhất một tài khoản quảng cáo',
            'email.email' => 'Sai định dạng email',
        ];
    }

    public function store()
    {
        if (!$this->filled('status')) {
            $this->merge([
                'status' => 0,
            ]);
        }

        if (!$this->filled('department_id')) {
            $this->merge([
                'department_id' => 0,
            ]);
        }



        $user = User::create(array_merge($this->all(), ['password' => md5(time())]));

        if ($this->filled('contents')) {
            Content::whereNull('map_user_id')->whereIn('id', $this->get('contents'))->update(['map_user_id' => $user->id]);
        }

        return $this;
    }

    public function save($id)
    {
        $user = User::findOrFail($id);

        if (!$this->filled('status')) {
            $this->merge([
                'status' => 0,
            ]);
        }

        if (!$this->filled('department_id')) {
            $this->merge([
                'department_id' => 0,
            ]);
        }



        $user->update($this->all());


        if ($this->filled('contents')) {

            Content::whereNull('map_user_id')->whereIn('id', $this->get('contents'))->update(['map_user_id' => $user->id]);
        } else {
            Content::where('map_user_id', $user->id)->update(['map_user_id' => null]);
        }

        return $this;
    }
}
