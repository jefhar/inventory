<?php

namespace App\WorkOrders\Requests;

use Domain\WorkOrders\Client;
use Domain\WorkOrders\Person;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class WorkOrderStoreRequest
 *
 * @package App\Admin\Requests
 */
class WorkOrderStoreRequest extends FormRequest
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
            Client::COMPANY_NAME => ['required', 'string'],
            Person::FIRST_NAME => ['string', 'nullable'],
            Person::LAST_NAME => ['string', 'nullable'],
            Person::PHONE_NUMBER => ['string', 'nullable', 'min:10', 'max:16'],
            Person::EMAIL => ['string', 'nullable'],
        ];
    }

    public function attributes()
    {
        return [
            Client::COMPANY_NAME => 'Company Name',
        ];
    }
}
