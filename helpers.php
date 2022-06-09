<?php

if (!function_exists('onlyNumber')) {

    function onlyNumber(?string $param)
    {
        if (empty($param)) {
            return null;
        }
        return preg_replace('/[^0-9]/', '', $param);
    }
}

if (!function_exists('convertStringToDouble')) {

    function convertStringToDouble(?string $value, $default = null)
    {
        if (empty($value)) {
            return $default;
        }

        if (is_numeric($value)){
            return round($value, 2);
        }

        if (str_contains(substr($value, -3), '.')) {
            return floatval(str_replace(',', '', $value));
        }

        return floatval(str_replace(',', '.', str_replace('.', '', $value)));
    }
}

if (!function_exists('convertStringToDate')) {

    function convertStringToDate(?string $param, $default = null)
    {
        if (empty($param)) {
            return $default;
        }

        $dateTimeArray = explode(' ', $param);
        $time = !empty($dateTimeArray[1]) ? ' ' . $dateTimeArray[1] : '';
        $param = $dateTimeArray[0];

        if (validDateFormat($param, 'Y-m-d')) {
            return $param . $time;
        }

        list($day, $month, $year) = explode('/', $param);
        return (new \DateTime($year . '-' . $month . '-' . $day))->format('Y-m-d') . $time;
    }
}

/**
 * Convert double format to BRL string
 */
if (!function_exists('convertFloatToBRL')) {

    function convertFloatToBRL($value, $withRS = false)
    {
        return ($withRS ? 'R$ ' : '') . number_format($value, 2, ',', '.');
    }
}

if (!function_exists('validDateFormat')) {

    function validDateFormat(?string $date, $format = 'd/m/Y H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}

if (!function_exists('validCpf')) {

    function validCpf($cpf)
    {
        // Extrai somente os números
        $cpf = preg_replace('/[^0-9]/is', '', $cpf);

        // Verifica se foi informado todos os digitos corretamente
        if (strlen($cpf) != 11) {
            return false;
        }

        // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Faz o calculo para validar o CPF
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }
}

if (!function_exists('validDDD')) {

    /**
     * Valid DDD
     *
     * @param $ddd
     * @return bool true is valid
     */
    function validDDD($ddd)
    {
        $invalids = ['25', '26', '29', '36', '39', '52', '72', '76', '78'];
        if ($ddd < 11 || $ddd > 99 || substr($ddd, '-1') == 0 || in_array($ddd, $invalids)) {
            return false;
        }
        return true;
    }
}

if (!function_exists('phoneValidate')) {
    /**
     * A função abaixo demonstra o uso de uma expressão regular que identifica, de forma simples, telefones válidos no Brasil.
     * Nenhum DDD iniciado por 0 é aceito, e nenhum número de telefone pode iniciar com 0 ou 1.
     * Exemplos válidos: +55 (11) 98888-8888 / 9999-9999 / 21 98888-8888 / 5511988888888
     *
     * @param $phone
     * @return bool
     */
    function phoneValidate($phone)
    {
        $phone = ltrim($phone, '0');//Remove zero on left
        return preg_match('/^(?:(?:\+|00)?(55)\s?)?(?:\(?([1-9][0-9])\)?\s?)?(?:((?:9\d|[2-9])\d{3})\-?(\d{4}))$/',
            $phone);
    }
}

/**
 * PHP Máscara CNPJ, CPF, Data e qualquer outra coisa
 * echo mask($cnpj, '##.###.###/####-##')
 * echo mask($cpf, '###.###.###-##')
 * echo mask($cep, '#####-###')
 * echo mask($data, '##/##/####')
 * echo mask($data, '[##][##][####]')
 * echo mask($data, '(##)(##)(####)')
 * echo mask($hora, 'Agora são ## horas ## minutos e ## segundos')
 * echo mask($hora, '##:##:##');
 */
if (!function_exists('mask')) {

    function mask($val, $mask)
    {
        $maskared = '';
        $k = 0;
        for ($i = 0; $i <= strlen($mask) - 1; ++$i) {
            if ($mask[$i] == '#') {
                if (isset($val[$k])) {
                    $maskared .= $val[$k++];
                }
            } else {
                if (isset($mask[$i])) {
                    $maskared .= $mask[$i];
                }
            }
        }
        return $maskared;
    }
}

if (!function_exists('phoneMask')) {

    function phoneMask($val)
    {
        if (empty($val = onlyNumber($val))) {
            return '';
        }

        if (strlen($val) === 11) {
            return mask($val, '(##) #####-####');
        }
        return mask($val, '(##) ####-####');
    }
}

if (!function_exists('documentMask')) {

    function documentMask($val)
    {
        if (empty($val = onlyNumber($val))) {
            return '';
        }

        //CPF
        if (strlen($val) === 11) {
            return mask($val, '###.###.###-##');
        }

        //CNPJ
        if (strlen($val) === 14) {
            return mask($val, '##.###.###/####-##');
        }

        return $val;
    }
}
