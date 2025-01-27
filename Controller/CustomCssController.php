<?php

/*
 * This file is part of the CustomCSSBundle.
 * All rights reserved by Kevin Papst (www.kevinpapst.de).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\CustomCSSBundle\Controller;

use App\Controller\AbstractController;
use KimaiPlugin\CustomCSSBundle\Entity\CustomCss;
use KimaiPlugin\CustomCSSBundle\Form\CustomCssType;
use KimaiPlugin\CustomCSSBundle\Repository\CustomCssRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/custom-css")
 * @Security("is_granted('edit_custom_css')")
 */
class CustomCssController extends AbstractController
{
    /**
     * @Route(path="", name="custom_css", methods={"GET", "POST"})
     */
    public function indexAction(Request $request, CustomCssRepository $repository): Response
    {
        $entity = $repository->getCustomCss();

        $form = $this->createForm(CustomCssType::class, $entity, [
            'action' => $this->generateUrl('custom_css'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CustomCss $entity */
            $entity = $form->getData();
            try {
                $repository->saveCustomCss($entity);
                $this->flashSuccess('action.update.success');
            } catch (\Exception $ex) {
                $this->flashError($ex->getMessage());
            }
        }

        $rulesets = [];
        if ($this->isGranted('select_custom_css')) {
            $rulesets = $repository->getPredefinedStyles();
        }

        return $this->render('@CustomCSS/index.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
            'rulesets' => $rulesets,
        ]);
    }
}
