<?php

namespace App\Form;

use App\Entity\Recipe;
use DateTimeImmutable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints\Length;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ["label" => "Titre"])
            ->add('slug', TextType::class, [
                "required" => false,
                "constraints" => new Length(min: 5)
            ])
            ->add('content'  ,TextType::class, ["label" => "Contenu"])
            // ->add('createdAt', null, [
            //     'widget' => 'single_text'
            // ])
            // ->add('updateAt', null, [
            //     'widget' => 'single_text'
            // ])
            ->add('duration', TextType::class, ["label" => "Durée"])
            ->add("save", SubmitType::class, [
                "label" => "Enregistrer"
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->autoSlug(...))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->attachTimestamps(...));
        
    }

    public function autoSlug (PreSubmitEvent $event) {
        $data = $event->getData();
        if (empty($data["slug"])) {
            $slugger = new AsciiSlugger();
            $data["slug"] = strtolower($slugger->slug($data["title"]));
            $event->setData($data);
        }
    }

    public function attachTimestamps (PostSubmitEvent $event):void {
        $data = $event->getData();
        if (!($data instanceof Recipe)) {
            return;
        }

        $data->setUpdateAt(new DateTimeImmutable());

        if (!$data->getId()) {
            $data->setCreatedAt(new DateTimeImmutable());
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
