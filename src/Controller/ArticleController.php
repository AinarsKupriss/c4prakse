<?php
    namespace App\Controller;

    use App\Entity\Article;


    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\Extension\Core\Type\TextareaType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Flex\Response;

    class ArticleController extends  Controller{

        /**
         * @Route("/", name="articles")
         */
        public function index(){
            //return new Response('<html><body>Hello</body></html>');

            $articles = $this->getDoctrine()->getRepository(Article::class)->findAll();
            return $this->render('articles/index.html.twig', array('articles' => $articles));
        }

        /**
         * @Route("/article/new", name="new article")
         * Method({"GET", "POST"})
         */
        public function new(Request $request){
            $article = new Article();
            $form = $this->createFormBuilder($article)
                ->add('title', TextType::class, array('attr' => array('class' => 'form-control')))
                ->add('body', TextareaType::class, array('attr' => array('class' => 'form-control')))
                ->add('save', SubmitType::class, array('label' => 'Create', 'attr' => array('class' => 'btn btn-primary mt-3')))
                ->getForm();

            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $article = $form->getData();

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($article);
                $entityManager->flush();

                return $this->redirectToRoute('articles');
            }

            return $this->render('articles/new.html.twig', array('form' => $form->createView()));
        }

        /**
         * @Route("/article/selected/{id}", name="article")
         */
        public function show($id){
            $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

            return $this->render('articles/selected/show.html.twig', array('article' => $article));
        }

        /**
         * #Route("/article/delete/{id}")
         * @Method ({"DELETE"})
         */
        public function delete(Request $request, $id){
            $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();

            $responce = new Response();
            $responce->send();

            return $this->render('articles/selected/show.html.twig', array('article' => $article));
        }



//        /**
//         * @Route("/")
//         */
//        public function save(){
//            $entityManager = $this->getDoctrine()->getManager();
//
//            $article = new Article();
//            $article->setTitle('Article 3');
//            $article->setBody('This is an article about the feudal monarchies of the middle ages.');
//
//            $entityManager->persist($article);
//
//            $entityManager->flush();
//            return new Response('Saved an article with the id of ' .$article->getId());
//        }
    }