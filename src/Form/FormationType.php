<?php

namespace App\Form;

use App\Entity\Formation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as SFType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

// CHECK : https://symfony.com/doc/current/form/create_custom_field_type.html

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateDebut', 
            SFType\DateType::class, [
                'label' => 'Date de début',
                'input' => 'datetime',
                'widget' => 'single_text',
                'data' => new \DateTime("now"), 
                'attr' => array(
                    'class' => 'form-control', 
                    'style' => 'margin-bottom:15px;margin-left: auto;margin-right: auto;width:500px')])
            ->add('nbreHeures', 
            SFType\IntegerType::class, [
                'label' => 'Le nombre d\'heures', 
                'attr' => array(
                    'class' => 'form-control', 
                    'style' => 'margin-bottom:15px;margin-left: auto;margin-right: auto;width:500px')])
            ->add('departement', 
            SFType\TextType::class, [
                'label' => 'Le département', 
                'attr' => array(
                    'class' => 'form-control', 
                    'style' => 'margin-bottom:15px;margin-left: auto;margin-right: auto;width:500px')])
            // <Castaing>
            ->add('sommaire', 
            SFType\TextType::class, [
                'label' => 'Le Sommaire', 
                'attr' => array(
                    'class' => 'form-control', 
                    'style' => 'margin-bottom:15px;margin-left: auto;margin-right: auto;width:500px')])
            // </Castaing>
            ->add('leProduit', 
            EntityType::class, 
            array(
                'class' => 'App\Entity\Produit', 
                'choice_label' => 'libelle', 
                'attr' => array(
                    'class' => 'form-control', 
                    'style' => 'margin-bottom:15px;margin-left: auto;margin-right: auto;width:500px')))
            ->add('Enregistrer', 
            SFType\SubmitType::class, 
            array(
                'label'=> 'Enregistrer', 
                'attr' => array(
                    'class' => 'btn btn-primary', 
                    'style' => 'margin-top:15px;margin-left: auto;margin-right: auto;width:500px')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Formation::class]);
    }
}
