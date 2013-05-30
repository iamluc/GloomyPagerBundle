<?php

namespace Gloomy\PagerBundle\Serializer\Handler;

use JMS\Serializer\Handler\SubscribingHandlerInterface;
// use JMS\Serializer\XmlSerializationVisitor;
// use JMS\Serializer\YamlSerializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\GenericSerializationVisitor;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Context;

use Gloomy\PagerBundle\Pager\Pager;

class GloomyPagerHandler implements SubscribingHandlerInterface
{
    private $translator;

    public static function getSubscribingMethods()
    {
        $methods = array();
        foreach (array('json') as $format) { // TODO: xml, yml
            $methods[] = array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'type' => 'Gloomy\PagerBundle\Pager\Pager',
                'format' => $format,
            );
        }

        return $methods;
    }

    public function serializePagerToJson(JsonSerializationVisitor $visitor, Pager $form, array $type, Context $context)
    {
        return $this->convertPagerToArray($visitor, $form, $type, $context);
    }

    private function convertPagerToArray(GenericSerializationVisitor $visitor, Pager $pager, array $type, Context $context)
    {
        $isRoot = null === $visitor->getRoot();

        $pg = $pager->getPages();
        $result = array(
            'page'               => $pg->current,
            'page_count'         => $pg->last,

            'per_page'           => $pg->itemCountPerPage,
            'total_item_count'   => $pg->totalItemCount,

            'first_item_number'  => $pg->firstItemNumber,
            'last_item_number'   => $pg->lastItemNumber,
            'current_item_count' => $pg->currentItemCount,

            'items'              => $visitor->getNavigator()->accept($pager->getItems(), array('name' => 'array'), $context)
            );

        if ($isRoot) {
            $visitor->setRoot($result);
        }

        return $result;
    }
}