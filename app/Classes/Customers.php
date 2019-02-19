<?php

namespace app\Classes;

class Customers
{
    public static function getCustomers()
    {
        $customers = json_decode(file_get_contents(public_path().'/sources/ua/edrpou.json'), true);

//        $result = array_search($customerName, $customers);
//        $result = array_where($customers, function ($key, $value) use ($customerName) {
//            return !empty($value) && $value $customerName);
//            !empty($value) && ($value == $customerName);
//            if(!empty($value) && strpos($value, $customerName)) {
//                return $value;
//            }
//});
//        });
        
        
//        array_slice()

        return $customers;
    }
}