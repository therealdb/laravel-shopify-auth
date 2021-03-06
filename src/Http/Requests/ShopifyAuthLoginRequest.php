<?php

namespace TheRealDb\ShopifyAuth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShopifyAuthLoginRequest extends FormRequest
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
        return [
            'shop' => 'required|regex:/^(.*)\.myshopify.com+$/i'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'shop.required' => 'You must enter your domain to install the app.',
            'shop.regex' => 'You must use your shopify admin URL. (i.e. your-store.myshopify.com)'
        ];
    }
}
