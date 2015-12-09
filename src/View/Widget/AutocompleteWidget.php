<?php

namespace App\View\Widget;

use BootstrapUI\View\Widget\BasicWidget;
use Cake\Routing\Router;
use Cake\View\Form\ContextInterface;

class AutocompleteWidget extends BasicWidget
{

    /**
     * @inheritDoc
     */
    public function render(array $data, ContextInterface $context)
    {
        $data['data-autocomplete-url'] = Router::url($data['autocompleteUrl']);
        if (isset($data['autocompleteKey'])) {
            $data['data-autocomplete-key'] = $data['autocompleteKey'];
        }
        if (isset($data['autocompleteValue'])) {
            $data['data-autocomplete-value'] = $data['autocompleteValue'];
        }

        unset($data['autocompleteUrl'],$data['autocompleteKey'], $data['autocompleteValue']);

        $hiddenWidget = new \Cake\View\Widget\BasicWidget($this->_templates);

        $hiddenInputId = uniqid('autocomplete');
        $hiddenInput = $hiddenWidget->render([
            'name' => $data['name'],
            'type' => 'hidden',
            'data-autocomplete-id' => $hiddenInputId
        ], $context);

        $data['name'] = $data['name'] . '_value';
        $data['type'] = 'text';
        $data['data-autocomplete-value-id'] = $hiddenInputId;

        return parent::render($data, $context) . $hiddenInput;
    }

    /**
     * @inheritDoc
     */
    public function secureFields(array $data)
    {
        return parent::secureFields($data) + [
            $data['name'] . '_value'
        ];
    }
}
