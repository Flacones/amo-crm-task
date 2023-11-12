<?php

namespace App\Services;

use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Models\LeadModel;
use Exception;

/**
 * Вспомогательный класс для создания сделок с api AmoCRM
 * Class LeadModelService
 * @package App\Services
 */
class LeadModelService
{
    /**
     * Формирует полученные данные в методы для заполнения в сделку
     * @param array $request
     * @return LeadModel
     * @throws Exception
     */
    public function getLeadModel(array $request): LeadModel
    {
        $validatedParams = $this->validateLeadModelFields($request);
        $customPhoneField = (new MultitextCustomFieldValuesModel())
            ->setFieldCode('PHONE')
            ->setValues(
                (new MultitextCustomFieldValueCollection())
                    ->add(
                        (new MultitextCustomFieldValueModel())
                            ->setValue($validatedParams['phone'])
                    )
            );
        $customEmailField = (new MultitextCustomFieldValuesModel())
            ->setFieldCode('EMAIL')
            ->setValues(
                (new MultitextCustomFieldValueCollection())
                    ->add(
                        (new MultitextCustomFieldValueModel())
                            ->setValue($validatedParams['email'])
                    )
            );
        $contractCollection = (new ContactsCollection())
            ->add(
                (new ContactModel())
                    ->setFirstName($validatedParams['name'])
                    ->setCustomFieldsValues(
                        (new CustomFieldsValuesCollection())
                            ->add($customPhoneField)->add($customEmailField)
                    )
            );
        return (new LeadModel())
            ->setName('Тестовая сделка')
            ->setPrice((int)$validatedParams['price'])
            ->setContacts($contractCollection);
    }

    /**
     * Валидация запроса от клиента
     * @param array $request
     * @return array
     * @throws Exception
     */
    private function validateLeadModelFields(array $request): array
    {
        $validatedParams = [];
        $requiredParams = ['name', 'email', 'phone', 'price'];
        foreach ($requiredParams as $requiredParam) {
            if (!$request[$requiredParam]) {
                throw new Exception('Поле ' . $requiredParam . ' не заполнено');
            } else {
                $validatedParams[$requiredParam] = $request[$requiredParam];
            }
        }
        return $validatedParams;
    }
}
