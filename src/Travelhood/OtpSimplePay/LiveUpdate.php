<?php

namespace Travelhood\OtpSimplePay;

use Travelhood\OtpSimplePay\Exception\LiveUpdateException;

/**
 * @property ProductCollection $products
 */
class LiveUpdate extends Component
{
    const DEFAULT_FORM_ID = 'SimplePay_LiveUpdate_Form';
    const DEFAULT_SUBMIT_TEXT = 'Start SimplePay transaction';
    const HTML_FORM = '<form action="%{action}" method="%{method}" id="%{id}" accept-charset="UTF-8">' . PHP_EOL . '%{html}</form>' . PHP_EOL;
    const HTML_INPUT = '<input type="hidden" name="%{name}" value="%{value}" />' . PHP_EOL;
    const HTML_SUBMIT = '<button type="submit" form="%{form}">%{html}</button>' . PHP_EOL;

    /**
     * @param Order $order
     * @param null $formId
     * @param string $submit
     * @return string
     * @throws Exception\OrderException
     * @throws LiveUpdateException
     */
    public function generateForm(Order $order, $formId = null, $submit = self::DEFAULT_SUBMIT_TEXT)
    {
        if (!$formId) {
            $formId = self::DEFAULT_FORM_ID . '-' . $order->getOrderRef();
        }
        $order->setPricesCurrency($this->service->config->getCurrency());
        $order->validate();
        $inputs = '';
        foreach ($order->toArray() as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    $inputs .= Util::interpolateString(self::HTML_INPUT, [
                        'name' => $k . '[]',
                        'value' => $v2,
                    ]);
                }
            } else {
                $inputs .= Util::interpolateString(self::HTML_INPUT, [
                    'name' => $k,
                    'value' => $v,
                ]);
            }
        }
        if (is_callable($submit)) {
            $inputs .= trim($submit($formId)) . PHP_EOL;
        } elseif (is_string($submit)) {
            $n = substr_count($submit, '<');
            if ($n > 0 && $n == substr_count($submit, '>')) {
                $inputs .= trim($submit) . PHP_EOL;
            } else {
                $inputs .= Util::interpolateString(self::HTML_SUBMIT, [
                    'form' => $formId,
                    'html' => $submit,
                ]);
            }
        } else {
            throw new LiveUpdateException('Invalid parameter: $submit');
        }
        return Util::interpolateString(self::HTML_FORM, [
            'action' => $this->service->getUrlLiveUpdate(),
            'method' => 'POST',
            'id' => $formId,
            'html' => $inputs,
        ]);
    }
}