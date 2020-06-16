# MacAddressAssistant
Can check the MAC-address for validity/
Allows you to convert MAC-address in an arbirary format
## install:
`composer require skrip42/mac-address`
## usage:
```php
MacAddress::isValid($macAddress); //check MAC-address for validity
MacAddress::isHex($macAddress);   //check MAC-address for validity hex format
MaxAddress::isDec($macAddress);   //check MAC-address for validity dec format

MacAddress::fromHex($macAddress); //create MacAddress instance from any valid hex MAC-address
MacAddress::fromDec($macAddress); //create MacAddress instance from any valid dec MAC-address
MacAddress::fromAuto($macAddress); //create MacAddress instance from any valid MAC-address (hex format priority)

MacAddress->to6colon(); //return MAC-address in 'hh:hh:hh:hh:hh:hh' format
MacAddress->to6dash();  //return MAC-address in 'HH-HH-HH-HH-HH-HH' format
MacAddress->to3dot();   //return MAC-address in 'hhhh.hhhh.hhhh'
MacAddress->toFormat(   //return MAC-address in arbirary format
  $delimiter, //delimiter for you format
  $partCount, //count of MAC-address parts. for example f8f0.8216.242e has 3 parts, f8.f0.82.16.24.2e has 6.
  $mode,      //available value: 'hex','dec' default is 'hex'
  $full,      //use left-hand zero. default is true
  $upper,     //use uppercase format. default is false
  $skipNull   //skip empty parts. for example f8.f0.82.16..2e . default is false
);
```
## example:
```php
//validate mac address
MacAddress::isValid('12.3.6.1.2.4');    //true
MacAddress::isValid('12.128.4.1.2.4');  //true
MacAddress::isHex('12:128:4:1:2:4');    //false
MacAddress::isDec('12.128.4.1.2.4');    //true
MacAddress::isHex('1203.06f1.2e4f'):    //true
MacAddress::isDec('12-03-06-F1-2E-4F'); //false

//convert mac address
MacAddress->fromHex('12.00.1f.83.09.2e')->toFormat(':', 6, 'hex', true, false, false); //12:00:1f:83:09:2e
MacAddress->fromHex('12:00:1F:83:09:2e')->toFormat(':', 6, 'hex', true, false, true);  //12::1f:83:09:2e
MacAddress->fromHex('12..1f.83.9.2e')->toFormat('.', 3, 'hex', true, false, false); //1200.1f83.092e
MacAddress->fromHex('12 00 1F 83 09 2E')->toFormat('.', 6, 'hex', false, false, false); //12.0.1f.83.9.2e
MacAddress->fromHex('12.0.1f.83.9.2e')->toFormat('-', 6, 'hex', true, true, false);  //12-00-1F-83-09-2E
MacAddress->fromHex('12-00-1F-83-09-2E')->toFormat('.', 6, 'dec', false, false, false); //18.0.31.131.9.46
MacAddress->fromDec('19.0.31.131.9.46')->toFormat('.', 6, 'hex', false, false, false); //12.0.1f.83.9.2e
```
