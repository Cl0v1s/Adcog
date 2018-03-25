<?php

namespace Adcog\AdminBundle\Controller;

use Adcog\DefaultBundle\Entity\ContractType;
use Adcog\DefaultBundle\Repository\ContractTypeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class AdminContractType
 *
 * @author "Nicolas Drufin" <nicolas.drufin@ensc.fr>
 * @Route("/contract-type")
 */
class AdminContractTypeController extends Controller
{
    /**
     * Index
     *
     * @param Request $request Request
     *
     * @return array
     * @Route("/{page}", requirements={"page":"\d+"}, defaults={"page":1})
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        // Filter results
        $filter = $this->get('form.factory')->createNamed(null, 'adcog_admin_contract_type_filter', [], ['method' => 'GET', 'csrf_protection' => false])->handleRequest($request);
        $filterData = !$filter->isSubmitted() || $filter->isValid() ? $filter->getData() : [];

        // Find filtered results
        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:ContractType')->getPaginator($paginatorHelper, $filterData);

        return [
            'paginator' => $paginator,
            'filter' => $filter->createView(),
        ];
    }

    /**
     * Create
     *
     * @param Request $request Request
     *
     * @return array|RedirectResponse
     * @Route("/create")
     * @Method("GET|POST")
     * @Template()
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm('adcog_admin_contract_type', $contract = new ContractType());
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($contract);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_contract_type_read', ['contract' => $contract->getId()]));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Read
     *
     * @param ContractType $contract ContractType
     *
     * @return array
     * @Route("/{contract}/read", requirements={"contract":"\d+"})
     * @ParamConverter("contract", class="AdcogDefaultBundle:ContractType")
     * @Method("GET|POST")
     * @Template()
     */
    public function readAction(ContractType $contract)
    {
        return [
            'contract' => $contract,
        ];
    }

    /**
     * Update
     *
     * @param Request $request Request
     * @param ContractType  $contract  ContractType
     *
     * @return array|RedirectResponse
     * @Route("/{contract}/update", requirements={"contract":"\d+"})
     * @ParamConverter("contract", class="AdcogDefaultBundle:ContractType")
     * @Method("GET|POST")
     * @Template()
     */
    public function updateAction(Request $request, ContractType $contract)
    {
        $form = $this->createForm('adcog_admin_contract_type', $contract);
        if ($form->handleRequest($request)->isValid()) {
            $this->get('doctrine.orm.default_entity_manager')->flush();

            return $this->redirect($this->generateUrl('admin_contract_type_read', ['contract' => $contract->getId()]));
        }

        return [
            'contract' => $contract,
            'form' => $form->createView(),
        ];
    }

    /**
     * Delete
     *
     * @param Request $request Request
     * @param ContractType  $contract  ContractType
     *
     * @return array|RedirectResponse
     * @Route("/{contract}/delete", requirements={"contract":"\d+"})
     * @ParamConverter("contract", class="AdcogDefaultBundle:ContractType")
     * @Method("GET|POST")
     * @Template()
     */
    public function deleteAction(Request $request, ContractType $contract)
    {
        $form = $this->createForm('form');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->remove($contract);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_contract_type_index'));
        }

        return [
            'contract' => $contract,
            'form' => $form->createView(),
        ];
    }

    /**
     * Replace
     *
     * @param Request $request Request
     * @param ContractType  $contract  ContractType
     *
     * @return array|RedirectResponse
     * @Route("/{contract}/replace", requirements={"contract":"\d+"}, name="admin_contract_type_replace")
     * @ParamConverter("contract", class="AdcogDefaultBundle:ContractType")
     * @Method("GET|POST")
     * @Template()
     */
    public function replaceAction(Request $request, ContractType $contract)
    {
        $query = function (ContractTypeRepository $er) use ($contract) {
            $qb = $er->createQueryBuilder('a');

            return $qb
                ->andWhere($qb->expr()->neq('a', ':contract'))
                ->setParameter('contract', $contract);
        };

        $form = $this
            ->createFormBuilder()
            ->add('contract', 'entity', [
                'label' => 'Nouveau type de contrat',
                'class' => 'Adcog\DefaultBundle\Entity\ContractType',
                'query_builder' => $query,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->getForm();
        if ($form->handleRequest($request)->isValid()) {
            $new = $form->get('contract')->getData();
            $experiences = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Experience')->getPaginator(PaginatorHelper::createEmptyInstance(), [
                'contractType' => $contract,
            ]);
            foreach ($experiences as $experience) {
                $experience->setContractType($new);
            }
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->flush();
            $em->remove($contract);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_contract_type_index'));
        }

        return [
            'contract' => $contract,
            'form' => $form->createView(),
        ];
    }
}
