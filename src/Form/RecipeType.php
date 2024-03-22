<?php

namespace App\Form;

use App\Entity\Category;
use App\Form\FormListenerFactory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
// use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
// use Symfony\Component\Form\Event\PostSubmitEvent;
// use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
// use Symfony\Component\OptionsResolver\OptionsResolver;
// use Symfony\Component\String\Slugger\AsciiSlugger;
// use Symfony\Component\Validator\Constraints\Length;
// use Symfony\Component\Validator\Constraints\Regex;
// use Symfony\Component\Validator\Constraints\Sequentially;

class RecipeType extends AbstractType
{

    public function __construct(private FormListenerFactory $listenerFactory)
    {

    }


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'empty_data' => ''
            ])
            ->add('slug', TextType::class, [
                'required' => false
            ])
            ->add('category', EntityType::class, [
                'class'=> Category::class,
                'choice_label' => 'name'
            ])
            ->add('content', TextareaType::class, [
                'empty_data' => ''
            ])

            ->add('duration')
            ->add('save', SubmitType::class, [
                'label' => 'Enregister'
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->listenerFactory->autoSlug('title'))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->listenerFactory->timeStamps());
    }
}



// 'constraints' => new Sequentially([
//     new Length(min: 5),
//     new Regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', message: ('Ce slug est invalide'))
// ])
