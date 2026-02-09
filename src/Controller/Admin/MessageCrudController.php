<?php

namespace App\Controller\Admin;

use App\Entity\Message;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class MessageCrudController extends AbstractCrudController
{
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextareaField::new('content'),
            AssociationField::new('sender'),
            AssociationField::new('room'),
            DateTimeField::new('createdAt')->hideOnForm(),
        ];
    }

    public static function getEntityFqcn(): string
    {
        return Message::class;
    }
}
