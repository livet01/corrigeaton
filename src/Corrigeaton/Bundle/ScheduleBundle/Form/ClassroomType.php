<?php

namespace Corrigeaton\Bundle\ScheduleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ClassroomType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id')
            ->add('email')
        ;
        parent::buildForm($builder,$options);
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Corrigeaton\Bundle\ScheduleBundle\Entity\Classroom'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'corrigeaton_bundle_schedulebundle_classroom';
    }
}
