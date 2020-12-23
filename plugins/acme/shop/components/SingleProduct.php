<?php namespace Acme\Shop\Components;

use Acme\Shop\Models\Product;
use Acme\Shop\Models\Comment;

use Input;
use Validator;
use ValidationException;
use Flash;

class SingleProduct extends \Cms\Classes\ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'Товар',
            'description' => 'Показать товар'
        ];
    }

    public function onRun()
    {
        $this->prepareVars();
    }

    function prepareVars()
    {
        $id = $this->param('slug');
        $query = Product::where('slug', '=', $id)->first();

        if($query == null) {
            return \Response::make($this->controller->run('404'), 404);
        }
        $this->page['product'] = $query;
        $this->page['avg'] = ($query->comments()->avg('rating') / 10 * 100) * 2;
        $this->page['commentCount'] = $this->page['product']->comments()->where('status', 1)->count();

        $this->page->title = $this->page['product']->title;
        $this->page->meta_title = $this->page['product']->meta_title;
        $this->page->meta_description = $this->page['product']->meta_description;

    }

    public function onComment()
    {
        $rules = [
            'user_title'  => 'required|min:3',
            'user_email'  => 'required|email',
            'user_description' => 'required|min:10|max:300',
            'user_rating' => 'required|'
        ];
        $messages = [
            'required' => 'Поле обязательно к заполнению!',
            'email'    => 'E-mail имеет неверный формат!',
            'min'      => 'Минимум :min символов!',
            'max'      => 'Максимум :min символов!',
        ];
        $validator = Validator::make(Input::all(), $rules, $messages);
        if ($validator->fails()) { //если не прошло валидацию
            throw new ValidationException($validator);
        } else {
            $result = $this->insertComment(Input::all());
            $result ? Flash::success('Комментарий успешно добавлен на модерацию!') : Flash::error('Произошла ошибка!');
        }
    }

    private function insertComment($vars) {
        $comment = new Comment;

        $comment->product_id = $vars['product_id'];
        $comment->title = $vars['user_title'];
        $comment->email = $vars['user_email'];
        $comment->description = $vars['user_description'];
        $comment->rating = $vars['user_rating'];
        $query = $comment->save();
        return $query;
    }
}
