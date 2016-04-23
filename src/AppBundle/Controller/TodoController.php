<?php
/**
 * Created by PhpStorm.
 * User: tomislavhorvat
 * Date: 21.04.2016.
 * Time: 09:14
 */

namespace AppBundle\Controller;

use AppBundle\Entity\todolist2;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;

class TodoController extends Controller
{
    /**
     * @Route("/", name="todo_list")
     */
    public function listAction()
    {
        $todos = $this->getDoctrine()->getRepository('AppBundle:todolist2')->findAll();


        return $this->render('todo/index.html.twig', array(
            'todos' => $todos
        ));

    }

    /**
     * @Route("/create", name="todo_create")
     */
    public function createAction(Request $request)
    {
        $todo = new todolist2;

         $form = $this->createFormBuilder($todo)
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('category', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('description', TextareaType::class, array(
                'attr' => array(
                    'class' => 'form-control',
                    'style' => 'margin-bottom:15px')))
            ->add('priority', ChoiceType::class, array(
                'choices' => array(
                    'Low' => 'Low',
                    'Normal' => 'Normal',
                    'High' => 'High'
                ),
                'attr' => array(
                    'class' => 'form-control',
                    'style' => 'margin-bottom:15px'
                ),
                'placeholder' => 'Odaberi prioritet'
            ))
            ->add('due_date', DateTimeType::class, array('attr' => array('class' => 'ico', 'style' => 'margin: 0px 0px 15px 0px')))
            ->add('save', SubmitType::class, array(
                'label' => 'Create Todo',
                'attr' => array(
                    'class' => 'btn btn-primary',
                    'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            // Get data
            $name = $form['name']->getData();
            $category = $form['category']->getData();
            $description = $form['description']->getData();
            $priority = $form['priority']->getData();
            $due_date = $form['due_date']->getData();
            $now = new \DateTime('now');

            $todo->setName($name);
            $todo->setCategory($category);
            $todo->setDescription($description);
            $todo->setPriority($priority);
            $todo->setDueDate($due_date);
            $todo->setCreateDate($now);

            $em = $this->getDoctrine()->getManager();
            $em->persist($todo);
            $em->flush();

            $this->addFlash(
                'notice' , 'Todo Added'
            );

            return $this->redirectToRoute('todo_list');



        }

        return $this->render('todo/create.html.twig', array(
            'form' => $form->createView()
        ));

    }

    /**
     * @Route("/edit/{id}", name="todo_edit")
     */
    public function editAction($id, Request $request)
    {
        $todo = $this->getDoctrine()->getRepository('AppBundle:todolist2')->find($id);
        $now = new \DateTime('now');

        $todo->setName($todo->getName());
        $todo->setCategory($todo->getCategory());
        $todo->setDescription($todo->getDescription());
        $todo->setPriority($todo->getPriority());
        $todo->setDueDate($todo->getDueDate());
        $todo->setCreateDate($now);


        $form = $this->createFormBuilder($todo)
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('category', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('description', TextareaType::class, array(
                'attr' => array(
                    'class' => 'form-control',
                    'style' => 'margin-bottom:15px')))
            ->add('priority', ChoiceType::class, array(
                'choices' => array(
                    'Low' => 'Low',
                    'Normal' => 'Normal',
                    'High' => 'High'
                ),
                'attr' => array(
                    'class' => 'form-control',
                    'style' => 'margin-bottom:15px'
                ),
                'placeholder' => 'Odaberi prioritet'
            ))
            ->add('due_date', DateTimeType::class, array('attr' => array('class' => 'ico', 'style' => 'margin: 0px 0px 15px 0px')))
            ->add('save', SubmitType::class, array(
                'label' => 'Update Todo',
                'attr' => array(
                    'class' => 'btn btn-primary',
                    'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            // Get data
            $name = $form['name']->getData();
            $category = $form['category']->getData();
            $description = $form['description']->getData();
            $priority = $form['priority']->getData();
            $due_date = $form['due_date']->getData();
            $now = new \DateTime('now');

            $em = $this->getDoctrine()->getManager();
            $todo = $em->getRepository('AppBundle:todolist2')->find($id);

            $todo->setName($name);
            $todo->setCategory($category);
            $todo->setDescription($description);
            $todo->setPriority($priority);
            $todo->setDueDate($due_date);
            $todo->setCreateDate($now);


            $em->flush();

            $this->addFlash(
                'notice', 'Todo Updated'
            );

            return $this->redirectToRoute('todo_list');
        }

        return $this->render('todo/edit.html.twig', array(
            'todo' => $todo,
            'form' => $form -> createView()
        ));

    }

    /**
     * @Route("/details/{id}", name="todo_details")
     */
    public function detailAction($id)
    {
        $todos = $this->getDoctrine()->getRepository('AppBundle:todolist2')->find($id);
        return $this->render('todo/details.html.twig', array(
            'todos' => $todos,

        ));
    }

    /**
     * @Route("/delete/{id}", name="todo_delete")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $todo = $em->getRepository('AppBundle:todolist2')->find($id);



        $this->addFlash(
            'notice', 'Todo Deleted'
        );

        return $this->redirectToRoute('todo_list');


    }


}
