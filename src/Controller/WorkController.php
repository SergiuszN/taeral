<?php

namespace App\Controller;

use App\Repository\DayRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class WorkController
 * @Route("/work")
 * @package App\Controller
 */
class WorkController extends AbstractController
{
    /**
     * @Route("/", name="work_index")
     */
    public function indexAction(DayRepository $dayRepository)
    {
        return $this->render('work/index.html.twig', [
            'days' => $dayRepository->findCurrentMonthForUser($this->getUser())
        ]);
    }

    /**
     * @Route("/create-month", name="work_create_month")
     */
    public function createMonthAction()
    {

    }
}
