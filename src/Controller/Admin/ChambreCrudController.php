<?php

namespace App\Controller\Admin;

use DateTime;
use App\Entity\Chambre;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ChambreCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Chambre::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('titre'),
            TextField::new('description_courte'),
            TextEditorField::new('description_longue')->onlyOnForms(),
            ImageField::new('photo')->setUploadDir('public/images/chambre')->setUploadedFileNamePattern('[slug]-[timestamp].[extension]')->onlyWhenUpdating()->setFormTypeOptions([
                'required' => false,
            ]),
            ImageField::new('photo')->setBasePath('images/produit')->setUploadDir('public/images/chambre')->setUploadedFileNamePattern('[slug]-[timestamp].[extension]')->onlyWhenCreating(),
            ImageField::new('photo')->setBasePath('images/produit')->hideOnForm(),
            NumberField::new('prix_journalier', 'Prix/jour'),
            DateTimeField::new('date_enregistrement')->setFormat('d M Y à H:m:s')->hideOnForm(),
        ];
    }

    public function createEntity(string $entityFqcn)
    {
        $chambre = new $entityFqcn;
        $chambre->setDateEnregistrement(new DateTime);
        return $chambre;
    }
}
