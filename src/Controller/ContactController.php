<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(Request  $request, MailerInterface  $mailer): Response
    {
        $data = new ContactDTO();

        //Fill the data
        $data->name = "Henry Euloge";
        $data->email = "henrynomo68@gmail.com";
        $data->message = "Ceci est le contenu du message";

        $form = $this->createForm(ContactType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $mail = (new TemplatedEmail())
             ->to($data->service)
             ->from($data->email)
             ->subject("Demande d'autorisation")
             ->htmlTemplate("emails/email.html.twig")
             ->context(["data" => $data]);
            
            $mailer->send($mail);
            $this->addFlash("success", "L'email a bien été envoyée");
            return $this->redirectToRoute("contact");
        }

        return $this->render('contact/contact.html.twig', [
            "data" => $data,
            "form" => $form
        ]);
    }
}
