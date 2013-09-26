<?php

namespace SDPHP\TodoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TaskType extends AbstractType
{

	/**
	 * @param FormBuilderInterface $builder
	 * @param array                $options
	 */
	public function buildForm( FormBuilderInterface $builder, array $options )
	{

		$builder
			->add( 'task' )
			//->add('status')
		;
	}

	/**
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions( OptionsResolverInterface $resolver )
	{

		$resolver->setDefaults(
			array(
				'data_class' => 'SDPHP\TodoBundle\Entity\Task'
			)
		);
	}

	/**
	 * @return string
	 */
	public function getName()
	{

		return 'sdphp_todobundle_task';
	}
}
