<?php

namespace Travelhood\OtpSimplePay;

/**
 * @property ProductCollection $products
 */
class LiveUpdate extends Component
{
    const DEFAULT_FORM_ID = 'SimplePay_LiveUpdate_Form';
    const DEFAULT_SUBMIT_TEXT = 'Start SimplePay transaction';
    const HTML_FORM = '<form action="%{action}" method="%{method}" id="%{id}" accept-charset="UTF-8">'.PHP_EOL.'%{html}</form>' . PHP_EOL;
    const HTML_INPUT = '<input type="hidden" name="%{name}" value="%{value}" />' . PHP_EOL;
    const HTML_SUBMIT = '<button type="submit" form="%{form}">%{html}</button>' . PHP_EOL;

    /**
     * @param Order $order
     * @param string $formId
     * @param string $submitText
     * @return string
     * @throws Exception\OrderException
     */
    public function generateForm(Order $order, $formId=null, $submitText=self::DEFAULT_SUBMIT_TEXT)
    {
        if(!$formId) {
            $formId = self::DEFAULT_FORM_ID . '-' . $order->getOrderRef();
        }
        $order->setPricesCurrency($this->service->config->getCurrency());
        $order->validate();
        $inputs = '';
        foreach($order->toArray() as $k=>$v) {
            if(is_array($v)) {
                foreach($v as $k2=>$v2) {
                    $inputs.= Util::interpolateString(self::HTML_INPUT, [
                        'name' => $k.'[]',
                        'value' => $v2,
                    ]);
                }
            }
            else {
                $inputs.= Util::interpolateString(self::HTML_INPUT, [
                    'name' => $k,
                    'value' => $v,
                ]);
            }
        }
        $inputs.= Util::interpolateString(self::HTML_SUBMIT, [
            'form' => $formId,
            'html' => $submitText,
        ]);
        return Util::interpolateString(self::HTML_FORM, [
            'action' => $this->service->getUrlLiveUpdate(),
            'method' => 'POST',
            'id' => $formId,
            'html' => $inputs,
        ]);
    }
}