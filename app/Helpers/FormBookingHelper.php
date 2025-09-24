<?php
namespace App\Helpers;

class FormBookingHelper{
    public static function fields():array{
        return [
            'name-appt' =>   [
                            'key' => 'name',
                            'label' => 'Full name',
                            'type' => 'text',
                            'required' => true,
                            'placeholder' => 'Isi Nama Lengkap Ananda'
                        ],
            'email-appt' =>   [
                            'key' => 'email',
                            'label' => 'Email',
                            'type' => 'email',
                            'required' => true,
                            'placeholder' => 'Isi Email Orang Tua atau Pendamping'
                        ],
            'name' =>   [
                            'key' => 'name',
                            'label' => 'Full name',
                            'type' => 'text',
                            'required' => true,
                        ],
            'email' =>   [
                            'key' => 'email',
                            'label' => 'Email',
                            'type' => 'email',
                            'required' => true,
                        ],
            'phone' =>   [
                            'key' => 'phone',
                            'label' => 'Phone Number',
                            'type' => 'phone',
                            'required' => true,
                        ],
            'date_of_birth' =>   [
                            'key' => 'date_of_birth',
                            'label' => 'Date of Birth',
                            'type' => 'date',
                            'required' => true,
                        ],
            'address' =>   [
                            'key' => 'address',
                            'label' => 'Address',
                            'type' => 'text',
                            'required' => true,
                        ],
            'emergency_number' =>   [
                            'key' => 'emergency_number',
                            'label' => 'Emergency Number',
                            'type' => 'phone',
                            'required' => true,
                        ],
            'passport_number' =>   [
                            'key' => 'passport_number',
                            'label' => 'Passport Number',
                            'type' => 'numeric',
                            'required' => true,
                        ],
            'passport_number_2' =>   [
                            'key' => 'passport_number',
                            'label' => 'Passport Number',
                            'type' => 'alphanumeric',
                            'required' => true,
                        ],
            'reason_for_visit' =>   [
                            'key' => 'reason_for_visit',
                            'label' => 'Reason for Visit',
                            'type' => 'text',
                            'required' => false,
                            'multiline' => true
                        ],
            'agent' =>   [
                            'key' => 'agent',
                            'label' => 'Agent',
                            'type' => 'text',
                            'required' => true,
                        ],
            'vaccine' =>   [
                            'key' => 'vaccine',
                            'label' => 'Vaccine',
                            'type' => 'text',
                            'required' => true,
                        ],
            'contract_number' =>   [
                            'key' => 'contract_number',
                            'label' => 'Contract Number',
                            'type' => 'numeric',
                            'required' => true,
                            'max' => '10',
                            'min' => '10',
                        ],
            'notes' =>   [
                            'key' => 'notes',
                            'label' => 'Notes',
                            'type' => 'text',
                            'required' => false,
                            'multiline' => true
                        ],
        ];
    }

    public static function form():array{
        $f = self::fields();

        return [
            'standard-ui' => [
                $f['name'],
                $f['email'],
                $f['phone'],
            ],
            'standard-ui-appt' => [
                $f['name'],
                $f['email'],
                $f['phone'],
                $f['notes'],
            ],
            'form-medical-child' => [
                $f['name-appt'],
                $f['email-appt'],
                $f['phone'],
                $f['notes'],
            ],
            'form-medical-1' => [
                $f['name'],
                $f['email'],
                $f['phone'],
                $f['date_of_birth'],
                $f['address'],
                $f['emergency_number'],
                $f['passport_number'],
                $f['reason_for_visit'],
            ],
            'form-medical-2' => [
                $f['name'],
                $f['phone'],
                $f['date_of_birth'],
                $f['passport_number_2'],
                $f['reason_for_visit'],
            ],
            'form-medical-3' => [
                $f['name'],
                $f['phone'],
                $f['email'],
                $f['date_of_birth'],
                $f['passport_number_2'],
                $f['reason_for_visit'],
                $f['agent'],
            ],
            'form-medical-4' => [
                $f['name'],
                $f['phone'],
                $f['date_of_birth'],
                $f['passport_number_2'],
            ],
            'form-medical-5' => [
                $f['name'],
                $f['phone'],
                $f['date_of_birth'],
                $f['passport_number_2'],
                $f['vaccine'],
            ],
            'form-financing' => [
                $f['name'],
                $f['phone'],
                $f['email'],
                $f['contract_number'],
            ],
        ];
    }

    public static function getForm(string $key, string $type): array
    {
        $forms = self::form();
        if($type == 'appointment' && $key == 'standard-ui'){
            return $forms['standard-ui-appt'];
        }
        return $forms[$key] ?? [];
    }
}