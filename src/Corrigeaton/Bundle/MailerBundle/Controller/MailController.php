<?php

namespace Corrigeaton\Bundle\MailerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class MailController extends Controller
{
    /**
     * @Route("/mail")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
}
