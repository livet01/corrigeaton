<?php

namespace Corrigeaton\Bundle\ScheduleBundle\Menu;

use Corrigeaton\Bundle\ScheduleBundle\Event\ConfigureMenuEvent;
use Knp\Menu\FactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class Builder
{

    private $factory;
    private $eventDispatcher;

    function __construct(FactoryInterface $factory, EventDispatcherInterface $eventDispatcher)
    {
        $this->factory = $factory;
        $this->eventDispatcher = $eventDispatcher;
    }


    public function mainMenu(Request $request)
    {
        $menu = $this->factory->createItem('root');

        $menu->setChildrenAttribute('id','side-menu');
        $menu->setChildrenAttribute('class','nav');

        $menu->addChild('Accueil', array(
            'route' => 'dashboard'
        ))->setExtra('logo','beer');
        $menu->addChild('Enseignants', array(
            'route' => 'teacher',
        ))->setExtra('logo','graduation-cap');
        $menu->addChild('Classes', array(
            'route' => 'classroom',
        ))->setExtra('logo','user');
        $menu->addChild('Examens', array(
            'route' => 'test',
        ))->setExtra('logo','gavel');

        $this->eventDispatcher->dispatch(ConfigureMenuEvent::CONFIGURE,new ConfigureMenuEvent($this->factory,$menu));

        return $menu;
    }
}