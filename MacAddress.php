<?php

namespace Skrip42\MacAddress;

class MacAddress
{
    const DELIMITER_PATTERN = '.:\s-';
    private $macAddress = '';

    private function __construct(string $macAddress)
    {
        $this->macAddress = $macAddress;
    }

    public static function fromAuto(string $macAddress) : MacAddress
    {
        try {
            return static::fromHex($macAddress);
        } catch (MacAddressException $e) {
            try {
                return static::fromDec($macAddress);
            } catch (MacAddressException $e) {
                throw new MacAddressException('uncorrect mac address format');
            }
        }
    }

    public static function fromDec(string $macAddress) : MacAddress
    {
        if (!static::isDec($macAddress)) {
            throw new MacAddressException($macAddress . ' is not decimal format');
        }
        $parts = preg_split('~[' . static::DELIMITER_PATTERN . ']~', $macAddress);
        $partLen = 12 / count($parts);
        foreach ($parts as &$part) {
            $part = dechex($part);
            $part = str_pad($part, $partLen, '0', STR_PAD_LEFT);
        }
        return new self(implode($parts));
    }

    public static function fromHex(string $macAddress) : MacAddress
    {
        if (!static::isHex($macAddress)) {
            throw new MacAddressException($macAddress . ' is not hex format');
        }
        $parts = preg_split('~[' . static::DELIMITER_PATTERN . ']~', $macAddress);
        $partLen = 12 / count($parts);
        foreach ($parts as &$part) {
            $part = str_pad($part, $partLen, '0', STR_PAD_LEFT);
        }
        return new self(implode($parts));
    }

    public static function isDec(string $macAddress) : bool
    {
        if (empty($macAddress)) {
            return false;
        }
        $macAddress = trim($macAddress);
        $macAddress = preg_replace('~\s+~', ' ', $macAddress);
        $macAddress = strtolower($macAddress);
        if (preg_match('~[^\d' . static::DELIMITER_PATTERN . ']~', $macAddress)) {
            return false;
        }
        $parts = preg_split('~[' . static::DELIMITER_PATTERN . ']~', $macAddress);
        switch (count($parts)) {
            case 12:
                foreach ($parts as $part) {
                    if (((int) $part) > 15) {
                        return false;
                    }
                }
                break;
            case 6:
                foreach ($parts as $part) {
                    if (((int) $part) > 255) {
                        return false;
                    }
                }
                break;
            case 4:
                foreach ($parts as $part) {
                    if (((int) $part) > 4095) {
                        return false;
                    }
                }
                break;
            case 3:
                foreach ($parts as $part) {
                    if (((int) $part) > 65535) {
                        return false;
                    }
                }
                break;
            default:
                return false;
        }
        return true;
    }

    public static function isHex(string $macAddress) : bool
    {
        if (empty($macAddress)) {
            return false;
        }
        $macAddress = trim($macAddress);
        $macAddress = preg_replace('~\s+~', ' ', $macAddress);
        $macAddress = strtolower($macAddress);
        if (preg_match('~[^\dabcdef' . static::DELIMITER_PATTERN . ']~', $macAddress)) {
            return false;
        }
        $parts = preg_split('~[' . static::DELIMITER_PATTERN . ']~', $macAddress);
        $partMaxLen = 12 / count($parts);
        foreach ($parts as $part) {
            if (strlen($part) > $partMaxLen) {
                return false;
            }
        }
        return true;
    }

    public static function isValid(string $macAddress) : bool
    {
        return static::isHex($macAddress) || static::isDec($macAddress);
    }

    public function toFormat(
        string $delimiter = ':',
        int $partCount = 6,
        string $mode = 'hex',
        bool $full = true,
        bool $upper = false,
        bool $skipNull = false
    ) : string {
        $partLen = 12 / $partCount;
        if ((12 % $partCount) > 0) {
            throw new MacAddressException('uncorrect part count');
        }
        if ($partLen == 12 && $mode == 'dec') {
            throw new MacAddressException('dec');
        }
        $parts = [];
        for ($i = 0; $i < $partCount; $i++) {
            $parts[] = substr($this->macAddress, $i * $partLen, $partLen);
        }
        if ($mode === 'dec') {
            foreach ($parts as &$part) {
                $part = strval(hexdec($part));
            }
            switch ($partCount) {
                case 12:
                    $partLen = 2;
                    break;
                case 6:
                    $partLen = 3;
                    break;
                case 4:
                    $partLen = 4;
                    break;
                case 3:
                    $partLen = 5;
                    break;
                default:
                    throw new MacAddressException('uncorrect part count');
            }
        }
        if ($full) {
            foreach ($parts as &$part) {
                $part = str_pad($part, $partLen, '0', STR_PAD_LEFT);
            }
        } else {
            foreach ($parts as &$part) {
                $part = ltrim($part, '0');
            }
        }
        if ($skipNull) {
            foreach ($parts as &$part) {
                if (hexdec($part) === 0) {
                    $part = '';
                }
            }
        } else {
            foreach ($parts as &$part) {
                if ($part === '') {
                    $part = '0';
                }
            }
        }
        $mac = implode($parts, $delimiter);
        if ($upper) {
            $mac = strtoupper($mac);
        }
        return $mac;
    }
}
