<?php

namespace Corrigeaton\Bundle\ScheduleBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $menu->setChildrenAttribute('id','side-menu');
        $menu->setChildrenAttribute('class','nav');

        $menu->addChild('Home', array(
            'route' => 'dashboard'
        ))->setExtra('logo','beer');
        $menu->addChild('Teacher', array(
            'route' => 'teacher',
        ))->setExtra('logo','graduation-cap');
        $menu->addChild('Classroom', array(
            'route' => 'classroom',
        ))->setExtra('logo','user');
        $menu->addChild('Test', array(
            'route' => 'test',
        ))->setExtra('logo','gavel');

        return $menu;
    }
}