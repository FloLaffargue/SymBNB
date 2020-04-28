<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUserController extends AbstractController
{
    /**
     * Permet de récupérer tous les utilisateurs
     * 
     * @Route("/admin/user/{page<\d+>?1}", name="admin_users_index")
     */
    public function index(PaginationService $pagination, $page)
    {
        $pagination->setEntityClass(User::class)
                   ->setCurrentPage($page);
        
        return $this->render('admin/user/index.html.twig', [
            'pagination' => $pagination
        ]);
    }
    /**
     * Permet d'éditer un utilisateur
     *
     * @Route("/admin/user/{id}/edit", name="admin_user_edit")
     * 
     * @return Response
     * 
     */
    public function edit(User $user, Request $request, RoleRepository $repo) {
       
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $manager = $this->getDoctrine()->getManager();
            
            // dump($user->getUserRoles());
            // die();
            foreach($user->getUserRoles() as $role) {

                if($role->getId() == null) {
                    $roleCopie = $repo->findOneByTitle($role->getTitle());
                    $user->removeUserRole($role);
                    $user->addUserRole($roleCopie);
                    // dump($roleCopie);
                    // die();
                } else {
                    $user->addUserRole($role);
                    $manager->persist($role);
                }
                
                // $role->addUser($user);
            }

            
            $manager->flush();

            $this->addFlash(
                'success', 
                "La modification de l'utilisateur n° {$user->getId()} a bien eu lieu"
            );
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user, 
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer un utilisateur
     *
     * @Route("admin/user/{id}/delete", name="admin_user_delete")
     * 
     * @param User $user
     * @return Response
     */
    public function delete(User $user, EntityManagerInterface $manager) {
        
        foreach($user->getAds() as $ad) {

            if(count($ad->getBookings()) > 0) {
                $this->addFlash(
                    'danger',
                    "L'utilisateur ne peut pas être supprimé car il possède des réservations" 
                );

                return $this->redirectToRoute('admin_users_index');
            }
        }

        $manager->remove($user);
        $manager->flush();  

        $this->addFlash(
            'success', 
            "L'utilisateur {$user->getFullName()} a bien été supprimé"
        );

        return $this->redirectToRoute('admin_users_index');
    }

}
