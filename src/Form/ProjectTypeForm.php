<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\ProjectCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ProjectTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
			->add('ProjectCategory', EntityType::class, [
				// looks for choices from this entity
				'class' => ProjectCategory::class,
			])
			->add('status', ChoiceType::class, array('choices' => array(  'Waiting for approval' => 'Waiting for approval',),	'empty_data' => 'Waiting for approval' ))
            ->add('dateCreated', DateType::class, ['label' => 'Date to start', 'widget' => 'single_text',])
            ->add('budget', MoneyType::class, ['data' => 1, ]) 
			->add('description', TextareaType::class, array( 'attr' => array( 'class' => 'keepbreaks' ) ) )
			->add('path', fileType::class, ['label' => 'Add a file']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
