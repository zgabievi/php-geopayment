<?php
/*
 * This file is part of the GeoPayment package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Longman\GeoPayment\Provider\Card\Bog;

use Longman\GeoPayment\Provider\AbstractXMLResponse;

class XMLResponse extends AbstractXMLResponse
{

    public function error($code = 2, $desc = 'Unable to accept this payment')
    {
        if ($this->mode == 'check') {
            $this->content = $this->getCheckErrorBody($code, $desc);
        } elseif ($this->mode == 'reg') {
            $this->content = $this->getRegErrorBody($code, $desc);
        }
        return $this;
    }

    public function success($data = [])
    {
        if ($this->mode == 'check') {
            $this->content = $this->getCheckSuccessBody($data);
        } elseif ($this->mode == 'reg') {
            $this->content = $this->getRegSuccessBody($data);
        }
        return $this;
    }

    protected function getCheckSuccessBody($data)
    {
        $trx_id     = $this->clean($data['trx_id'], 50);
        $short_desc = $this->clean($data['short_desc'], 30);
        $long_desc  = $this->clean($data['long_desc'], 125);
        $account_id = $this->clean($data['account_id'], 32);
        $amount     = intval($data['amount']);
        $currency   = $this->clean($data['currency']);

        $content = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<payment-avail-response>
    <result>
        <code>1</code>
        <desc>OK</desc>
    </result>
    <merchant-trx>{$trx_id}</merchant-trx>
    <purchase>
        <shortDesc>{$short_desc}<shortDesc>
        <longDesc>{$long_desc}<longDesc>
        <account-amount>
            <id>{$account_id}</id>
            <amount>{$amount}</amount>
            <currency>{$currency}</currency>
            <exponent>2</exponent>
        </account-amount>
    </purchase>
</payment-avail-response>
XML;

        return $content;
    }

    protected function getCheckErrorBody($code, $desc = 'Unable to accept this payment')
    {
        $desc = $this->clean($desc, 125);

        $content = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<payment-avail-response>
    <result>
        <code>2</code>
        <desc>{$desc}</desc>
    </result>
</payment-avail-response>
XML;
        return $content;
    }

    protected function getRegSuccessBody($data)
    {
        $content = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<register-payment-response>
    <result>
        <code>1</code>
        <desc>OK</desc>
    </result>
</register-payment-response>
XML;

        return $content;
    }

    protected function getRegErrorBody($code, $desc = 'Unable to accept this payment')
    {
        $desc = $this->clean($desc, 125);

        $content = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<register-payment-response>
    <result>
        <code>2</code>
        <desc>{$desc}</desc>
    </result>
</register-payment-response>
XML;
        return $content;
    }
}
