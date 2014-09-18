<?php

namespace Corrigeaton\Bundle\ReportBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ReportType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isFinished')
            ->add('log')
            ->add('date')
            ->add('type')
            ->add('data')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Corrigeaton\Bundle\ReportBundle\Entity\Report'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'corrigeaton_bundle_reportbundle_report';
    }
}
