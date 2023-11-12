<?php


namespace App\Services;


use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Models\LeadModel;

/**
 * Вспомогательный класс для создания сделок с api AmoCRM
 * Class LeadModelService
 * @package App\Services
 */
class LeadModelService
{
    /**
     * Формирует полученные данные в методы для заполнение в сделку
     * @param array $request
     * @return \AmoCRM\Models\LeadModel
     * @throws \Exception
     */
    public static function getLeadModel(array $request): LeadModel
    {

        $requiredParams = ['name', 'email', 'phone', 'price'];
        foreach ($requiredParams as $requiredParam) {
            if (!$request[$requiredParam]) {
                throw new \Exception('Поле ' . $requiredParam . ' не заполнено');
            }
        }
        $customPhoneField = (new MultitextCustomFieldValuesModel())
            ->setFieldCode('PHONE')
            ->setValues(
                (new MultitextCustomFieldValueCollection())
                    ->add(
                        (new MultitextCustomFieldValueModel())
                            ->setValue($request['phone'])
                    )
            );
        $customEmailField = (new MultitextCustomFieldValuesModel())
            ->setFieldCode('EMAIL')
            ->setValues(
                (new MultitextCustomFieldValueCollection())
                    ->add(
                        (new MultitextCustomFieldValueModel())
                            ->setValue($request['email'])
                    )
            );
        $contractCollection = (new ContactsCollection())
            ->add(
                (new ContactModel())
                    ->setFirstName($request['name'])
                    ->setCustomFieldsValues(
                        (new CustomFieldsValuesCollection())
                            ->add($customPhoneField)->add($customEmailField)
                    )
            );
        return (new LeadModel())
            ->setName('Тестовая сделка')
            ->setPrice((int)$request['price'])
            ->setContacts($contractCollection);
    }
}
