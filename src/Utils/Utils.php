<?php

namespace App\Utils;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class Utils
{
    /**
     * @throws TransportExceptionInterface
     */
    public static function ping(string $url): bool
    {
        $client = HttpClient::create();
        $response = $client->request('HEAD', $url);

        return 200 === $response->getStatusCode();
    }

    public static function cleanString(string $str): string
    {
        return \trim($str);
    }

    public static function stringToInt(string $str): int
    {
        return \intval(Utils::cleanString($str));
    }

    public static function replaceMultipleSpacesByOne(string $str): string
    {
        return preg_replace('!\s+!', ' ', $str) ?? $str;
    }

    /**
     * @return string[]
     */
    public static function splitStringInTwoByLastWhitespace(string $str): array
    {
        return preg_split("/\s+(?=\S*+$)/", $str) ?: [];
    }

    public static function romanToNumber(string $roman): int
    {
        $romans = [
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1,
        ];

        $result = 0;
        foreach ($romans as $key => $value) {
            while (str_starts_with($roman, $key)) {
                $result += $value;
                $roman = substr($roman, strlen($key));
            }
        }

        return $result;
    }
}
