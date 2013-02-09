<?php

namespace HT\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use HT\AppBundle\Entity\Party;

use HT\AppBundle\Form\PartyType;
use HT\AppBundle\Form\PartySearchType;
use HT\AppBundle\Form\SearchType;

/**
 * Party controller.
 *
 * @Route("/party")
 */
class PartyController extends Controller
{
    /**
     * Displays a form to create a new Party entity.
     *
     * @Route("/new", name="party_new")
     * @Template()
     */
    public function newAction()
    {
        $party = new Party();
        $form   = $this->createForm(new PartyType(), $party);

        return array(
            'party' => $party,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Party entity.
     *
     * @Route("/create", name="party_create")
     * @Method("POST")
     * @Template("HTAppBundle:Party:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $party  = new Party();
        $form = $this->createForm(new PartyType(), $party);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($party);
            $em->flush();

            $request->getSession()->set('code', $party->getCode());
            $request->getSession()->set('owner', true);

            return $this->redirect($this->generateUrl('party_show'));
        }

        return array(
            'party' => $party,
            'form'   => $form->createView(),
        );
    }

    /**
     * @Route("/join", name="party_join")
     * @Template
     */
    public function joinAction(Request $request)
    {
        $form = $this->createForm(new PartySearchType);

        if ('POST' == $request->getMethod()) {
            $form->bind($request);

            $code = $form->get('code')->getData();

            $em = $this->getDoctrine()->getManager();

            $party = $em->getRepository('HTAppBundle:Party')->findOneByCode($code);

            if (!$party) {
                throw $this->createNotFoundException('Unable to find this party to join.');
            }

            $request->getSession()->set('code', $code);
            $request->getSession()->set('owner', false);

            return $this->redirect($this->generateUrl('party_show'));
        }

        return array('form' => $form->createView());
    }

    /**
     * Finds and displays a Party entity.
     *
     * @Route("/", name="party_show")
     * @Template()
     */
    public function showAction(Request $request)
    {
        $code = $request->getSession()->get('code');

        if (!$code) {
            return $this->redirect($this->generateUrl('party_join'));
        }

        $em = $this->getDoctrine()->getManager();

        $party = $em->getRepository('HTAppBundle:Party')->findOneByCode($code);

        if (!$party) {
            throw $this->createNotFoundException('Unable to find your party.');
        }

        $searchForm = $this->createForm(new SearchType());

        return array(
            'party' => $party,
            'searchForm' => $searchForm->createView()
        );
    }

    /**
     * @Route("/tracks")
     * @Template
     */
    public function tracksAction(Request $request)
    {
        $tracks = array();

        if ($code = $request->getSession()->get('code')) {
            $em = $this->getDoctrine()->getManager();
            if ($party = $em->getRepository('HTAppBundle:Party')->findOneByCode($code)) {
                $tracks = $party->getTracks();
            }
        }

        return array('tracks' => $tracks);
    }
}
