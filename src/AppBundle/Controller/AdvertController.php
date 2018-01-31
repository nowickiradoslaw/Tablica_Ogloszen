<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Advert;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\File\File;
use AppBundle\Entity\Comment;


/**
 * Advert controller.
 *
 * @Route("advert")
 */
class AdvertController extends Controller
{
    /**
     * Lists all advert entities.
     *
     * @Route("/", name="advert_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $adverts = $em->getRepository('AppBundle:Advert')->findActiveAdverts();

        return $this->render('advert/index.html.twig', array(
            'adverts' => $adverts,
        ));
    }

    /**
     * Creates a new advert entity.
     *
     * @Route("/new", name="advert_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $advert = new Advert();
        $form = $this->createForm('AppBundle\Form\AdvertType', $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            $user = $this->getUser();
            $advert->setUser($user);
            
                        
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $advert->getPhoto();

            // Generate a unique name for the file before saving it
            $fileName = md5(uniqid()).'.'.$file->guessExtension();

            // Move the file to the directory where brochures are stored
            $file->move(
                $this->getParameter('photo_directory'),
                $fileName
            );

            // instead of its contents
            $advert->setPhoto($fileName);
                     
            $em->persist($advert);
            $em->flush();

            return $this->redirectToRoute('advert_show', array('id' => $advert->getId()));
        }

        return $this->render('advert/new.html.twig', array(
            'advert' => $advert,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a advert entity.
     *
     * @Route("/{id}", name="advert_show", requirements={"id"="\d+"})
     */
    public function showAction(Request $request, Advert $advert)
    {
        $deleteForm = $this->createDeleteForm($advert);
        
        $comment = new Comment();
        
        $form = $this->createForm(
                'AppBundle\Form\CommentType', 
                $comment);
        $form->handleRequest($request);
                
        if($form->isSubmitted() && $form->isValid()){
            
            $em = $this->getDoctrine()->getManager();
            $advert->addComment($comment);
            $comment->setUser($this->getUser());
            
            $em->persist($advert);
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('advert_show', array('id' => $advert->getId()));            
            
    }

        return $this->render('advert/show.html.twig', array(
            'advert' => $advert,
            'comment_form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    /**
     * Finds and displays a advert entity.
     *
     * @Route("/{slug}", name="advert_show_category")
     * @Method("GET")
     */
    public function showCategoryAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository('AppBundle:Category')->findOneBy(['advertCategory'=>$slug]);
              
        return $this->render('advert/index.html.twig', array(
            'adverts' => $category->getAdverts(),
        ));
    }    

    /**
     * Displays a form to edit an existing advert entity.
     *
     * @Route("/{id}/edit", name="advert_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Advert $advert)
    {

        $file = new File($this->getParameter('photo_directory').'/'.$advert->getPhoto());
        $advert->setPhoto($file);
        
        $deleteForm = $this->createDeleteForm($advert);
        $editForm = $this->createForm('AppBundle\Form\AdvertType', $advert);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $advert->getPhoto();

            // Generate a unique name for the file before saving it
            $fileName = md5(uniqid()).'.'.$file->guessExtension();

            // Move the file to the directory where brochures are stored
            $file->move(
                $this->getParameter('photo_directory'),
                $fileName
            );

            // instead of its contents
            $advert->setPhoto($fileName);
            
            $em->persist($advert);
            $em->flush();
            
            return $this->redirectToRoute('advert_edit', array('id' => $advert->getId()));
        }

        return $this->render('advert/edit.html.twig', array(
            'advert' => $advert,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a advert entity.
     *
     * @Route("/{id}", name="advert_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Advert $advert)
    {
        $form = $this->createDeleteForm($advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            \unlink($this->getParameter('photo_directory').'/'.$advert->getPhoto());
            
            $em = $this->getDoctrine()->getManager();
            $em->remove($advert);
            $em->flush();
        }

        return $this->redirectToRoute('advert_index');
    }

    /**
     * Creates a form to delete a advert entity.
     *
     * @param Advert $advert The advert entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Advert $advert)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('advert_delete', array('id' => $advert->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
