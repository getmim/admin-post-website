<?php
/**
 * WebsiteController
 * @package admin-post-website
 * @version 0.0.1
 */

namespace AdminPostWebsite\Controller;

use LibFormatter\Library\Formatter;
use LibForm\Library\Form;
use LibPagination\Library\Paginator;
use PostWebsite\Model\PostWebsite as PWebsite;
use Post\Model\Post;

class WebsiteController extends \Admin\Controller
{
    private function getParams(string $title): array{
        return [
            '_meta' => [
                'title' => $title,
                'menus' => ['post', 'website']
            ],
            'subtitle' => $title,
            'pages' => null
        ];
    }

    public function editAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_post_website)
            return $this->show404();

        $website = (object)[];

        $id = $this->req->param->id;
        if($id){
            $website = PWebsite::getOne(['id'=>$id]);
            if(!$website)
                return $this->show404();
            $params = $this->getParams('Edit Post Website');
        }else{
            $params = $this->getParams('Create New Post Website');
        }

        $form           = new Form('admin.post-website.edit');
        $params['form'] = $form;

        if(!($valid = $form->validate($website)) || !$form->csrfTest('noob'))
            return $this->resp('post/website/edit', $params);

        if($id){
            if(!PWebsite::set((array)$valid, ['id'=>$id]))
                deb(PWebsite::lastError());
        }else{
            $valid->user = $this->user->id;
            if(!PWebsite::create((array)$valid))
                deb(PWebsite::lastError());
        }

        // add the log
        $this->addLog([
            'user'   => $this->user->id,
            'object' => $id,
            'parent' => 0,
            'method' => $id ? 2 : 1,
            'type'   => 'post-website',
            'original' => $website,
            'changes'  => $valid
        ]);

        $next = $this->router->to('adminPostWebsite');
        $this->res->redirect($next);
    }

    public function indexAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_post_website)
            return $this->show404();

        $cond = $pcond = [];
        if($q = $this->req->getQuery('q'))
            $pcond['q'] = $cond['q'] = $q;

        list($page, $rpp) = $this->req->getPager(25, 50);

        $websites = PWebsite::get($cond, $rpp, $page, ['name'=>true]) ?? [];
        if($websites)
            $websites = Formatter::formatMany('post-website', $websites, ['user']);

        $params             = $this->getParams('Post Website');
        $params['websites'] = $websites;
        $params['form']     = new Form('admin.post-website.index');

        $params['form']->validate( (object)$this->req->get() );

        // pagination
        $params['total'] = $total = PWebsite::count($cond);
        if($total > $rpp){
            $params['pages'] = new Paginator(
                $this->router->to('adminPostWebsite'),
                $total,
                $page,
                $rpp,
                10,
                $pcond
            );
        }

        $this->resp('post/website/index', $params);
    }

    public function removeAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_post_website)
            return $this->show404();

        $id      = $this->req->param->id;
        $website = PWebsite::getOne(['id'=>$id]);
        $next    = $this->router->to('adminPostWebsite');
        $form    = new Form('admin.post-website.index');

        if(!$website)
            return $this->show404();

        if(!$form->csrfTest('noob'))
            return $this->res->redirect($next);

        // add the log
        $this->addLog([
            'user'   => $this->user->id,
            'object' => $id,
            'parent' => 0,
            'method' => 3,
            'type'   => 'post-website',
            'original' => $website,
            'changes'  => null
        ]);

        PWebsite::remove(['id'=>$id]);
        Post::set(['website'=>null], ['website'=>$id]);

        $this->res->redirect($next);
    }
}