<?php

namespace App\Controller;

use DateTime;
use App\Entity\Ad;
use App\Entity\Booking;
use App\Form\BookingType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController
{
    /**
     * @Route("/ads/{slug}/book", name="booking_create")
     * @IsGranted("ROLE_USER")
     */
    public function book(Ad $ad, Request $request)
    {
        $booking = new Booking();

        $form = $this->createForm(BookingType::class, $booking);

        $form->handleRequest($request);

        dump($form);

        if($form->isSubmitted() && $form->isValid()) {

            // => Ici Les champs "created_at" et "amount" sont gérés directement dans le cycle de vie de l'entité
            $booking->setBooker($this->getUser())
                    ->setAd($ad);

            // Si les dates ne sont pas dispo, message d'erreur
            if(!$booking->isBookableDates()) {
                $this->addFlash(
                    'warning', 
                    "Les dates que vous avez choisi ne peuvent être réservées: elles sont déjà prises"
                );
            } else {
                // Sinon enreigistrement
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($booking);
                $manager->flush();

                return $this->redirectToRoute('booking_show', [
                    'id' => $booking->getId(),
                    'withAlert' => true
                ]);
            }

        }

        return $this->render('booking/book.html.twig', [
            'form' => $form->createView(),
            'ad' => $ad
        ]);
    }

    /**
     * Permet d'afficher la page d'une réservation
     *
     * @Route("/booking/{id}", name="booking_show" )
     * 
     * @param Booking $booking
     * @return Response
     */
    public function show(Booking $booking) {
        return $this->render('booking/show.html.twig', [
            'booking' => $booking
        ]);
    }

}
