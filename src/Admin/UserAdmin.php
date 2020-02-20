<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\User;
use App\Event\User\UserCreatedEvent;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class UserAdmin extends AbstractAdmin
{
    private $userPasswordEncoder;

    private $eventDispatcher;

    public function __construct($code, $class, $baseControllerName, UserPasswordEncoderInterface $userPasswordEncoder, EventDispatcherInterface $eventDispatcher)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->eventDispatcher = $eventDispatcher;
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('email')
            ->add('firstname')
            ->add('lastname')
        ;
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('Informations')
                ->add('email', EmailType::class)
                ->add('firstname', TextType::class)
                ->add('lastname', TextType::class)
                ->add('conditionsAccepted', CheckboxType::class, ['required' => true])
                ->add('optinCommercial')
            ->end()
            ->with('Security')
                ->add('roles', ChoiceType::class, [
                    'multiple' => true,
                    'expanded' => false,
                    'choices' => [
                        User::ROLE_USER => User::ROLE_USER,
                        User::ROLE_ADMIN => User::ROLE_ADMIN,
                    ],
                ])
                ->add('status', ChoiceType::class, [
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Active' => User::STATUS_ACTIVE,
                        'Inactive' => User::STATUS_INACTIVE,
                        'Locked' => User::STATUS_LOCKED,
                    ],
                ])
            ->end()
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('email')
            ->add('firstname')
            ->add('lastname')
            ->add('createdAt', 'date')
            ->add('roles')
            ->add('status', null, [
                'template' => 'admin/CRUD/user_list_status.html.twig',
                'row_align' => 'left',
            ])
            ->add('_action', null, [
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ],
            ])
        ;
    }

    /**
     * @param User $object
     */
    public function prePersist($object): void
    {
        if (!$object->getPassword()) {
            $object->setPassword(
                substr(sha1(uniqid($object->getEmail(), true)), 0, 10)
            );
        }

        // Users created by admin are set as active.
        $object->setStatus(User::STATUS_ACTIVE);
    }

    /**
     * @param User $object
     */
    public function postPersist($object): void
    {
        $event = new UserCreatedEvent($object, $object->getPassword());

        $object->setPassword(
            $this->userPasswordEncoder->encodePassword($object, $object->getPassword())
        );

        $this->getModelManager()->update($object);

        $this->eventDispatcher->dispatch($event);
    }
}
