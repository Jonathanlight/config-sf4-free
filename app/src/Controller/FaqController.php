<?php
/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 08/10/2018
 * Time: 00:12
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FaqController extends AbstractController
{
    /**
     * @Route("/faq/{_locale}", name="faq", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     */
    public function faq()
    {
        return $this->render('public/faq.html.twig');
    }

    /**
     * @Route("/faq/qu-est-qu-cryptomonnaie/{_locale}", name="faqCrypto", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     */
    public function faqCrypto()
    {
        return $this->render('faq/qu-est-qu-cryptomonnaie.html.twig');
    }

    /**
     * @Route("/faq/wallet/{_locale}", name="faqWallet", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     */
    public function faqWallet()
    {
        return $this->render('faq/faqWallet.html.twig');
    }

    /**
     * @Route("/faq/differente/crypto/{_locale}", name="faqDifferenteCrypto", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     */
    public function faqDifferenteCrypto()
    {
        return $this->render('faq/faqDifferenteCrypto.html.twig');
    }

    /**
     * @Route("/faq/depot/{_locale}", name="faqDepot", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     */
    public function faqDepot()
    {
        return $this->render('faq/faqDepot.html.twig');
    }

    /**
     * @Route("/faq/depot-autre-compte/{_locale}", name="faqDepotAutreCompte", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     */
    public function faqDepotAutreCompte()
    {
        return $this->render('faq/faqDepotAutreCompte.html.twig');
    }

    /**
     * @Route("/faq/temps-depot/{_locale}", name="faqTempsDepot", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     */
    public function faqTempsDepot()
    {
        return $this->render('faq/faqTempsDepot.html.twig');
    }

    /**
     * @Route("/faq/achat/{_locale}", name="faqAchat", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     */
    public function faqAchat()
    {
        return $this->render('faq/faqAchat.html.twig');
    }

    /**
     * @Route("/faq/vente/{_locale}", name="faqVente", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     */
    public function faqVente()
    {
        return $this->render('faq/faqVente.html.twig');
    }

    /**
     * @Route("/faq/frais/{_locale}", name="faqFrais", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     */
    public function faqFrais()
    {
        return $this->render('faq/faqFrais.html.twig');
    }

    /**
     * @Route("/faq/quantite-crypto/{_locale}", name="faqQuantiteCrypto", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     */
    public function faqQuantiteCrypto()
    {
        return $this->render('faq/faqQuantiteCrypto.html.twig');
    }

    /**
     * @Route("/faq/somme-euro/{_locale}", name="faqSommeEuro", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     */
    public function faqSommeEuro()
    {
        return $this->render('faq/faqSommeEuro.html.twig');
    }

    /**
     * @Route("/faq/annule-ordre/{_locale}", name="faqAnnuleOrdre", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     */
    public function faqAnnuleOrdre()
    {
        return $this->render('faq/faqAnnuleOrdre.html.twig');
    }

    /**
     * @Route("/faq/retrait-euro/{_locale}", name="faqRetraitEuro", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     */
    public function faqRetraitEuro()
    {
        return $this->render('faq/faqRetraitEuro.html.twig');
    }

    /**
     * @Route("/faq/faire-retrait-euro/{_locale}", name="faqFaireRetrait", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     */
    public function faqFaireRetrait()
    {
        return $this->render('faq/faqFaireRetrait.html.twig');
    }

    /**
     * @Route("/faq/retrait-autre-compte/{_locale}", name="faqRetraitAutreCompte", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     */
    public function faqRetraitAutreCompte()
    {
        return $this->render('faq/faqRetraitAutreCompte.html.twig');
    }

    /**
     * @Route("/faq/transfert-crypto/{_locale}", name="faqTransfertCrypto", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     */
    public function faqTransfertCrypto()
    {
        return $this->render('faq/faqTransfertCrypto.html.twig');
    }

    /**
     * @Route("/faq/compte/{_locale}", name="faqCompte", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     */
    public function faqCompte()
    {
        return $this->render('faq/faqCreationCompte.html.twig');
    }

    /**
     * @Route("/faq/verification/{_locale}", name="faqVerification", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     */
    public function faqVerification()
    {
        return $this->render('faq/faqVerifCompte.html.twig');
    }

    /**
     * @Route("/faq/verification-comment/{_locale}", name="faqVerificationHow", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     */
    public function faqVerificationHow()
    {
        return $this->render('faq/faqVerifCompteHow.html.twig');
    }

    /**
     * @Route("/faq/plusieurs-wallet/{_locale}", name="faqPlusieursWallet", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     */
    public function faqPlusieursWallet()
    {
        return $this->render('faq/faqPlusieursWallet.html.twig');
    }

    /**
     * @Route("/faq/email/{_locale}", name="faqEmail", methods={"GET","POST"}, defaults={"_locale" = "fr"})
     */
    public function faqEmail()
    {
        return $this->render('faq/faqChangeEmail.html.twig');
    }
}