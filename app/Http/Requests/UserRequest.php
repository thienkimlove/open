<?php

namespace App\Http\Requests;

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

    public function messages()
    {
        return [
            'name.required' => 'Vui lòng không để trống tên người dùng',
            'email.required' => 'Vui lòng không để trống email',
            'email.email' => 'Sai định dạng email',
        ];
    }

    public function store()
    {
        if (! isset($this->status)) {
            User::create(array_merge($this->all(), [
                'status' => 0,
            ]));

            return $this;
        }

        User::create(array_merge($this->all(), ['password' => md5(time())]));

        return $this;
    }

    public function save($id)
    {
        $user = User::findOrFail($id);

        if (! isset($this->status)) {
            $user->update(array_merge($this->all(), [
                'status' => 0
            ]));

            return $this;
        }

        $user->update($this->all());

        return $this;
    }
}
