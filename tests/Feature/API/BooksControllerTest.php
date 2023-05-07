<?php

namespace Tests\Feature\API;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class BooksControllerTest extends TestCase
{
    //Da refresh no banco de dados!
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_get_books_endpoint (): void
    {
        $books = Book::factory(3)->create();

        //Teste de rota
        $response = $this->getJson('/api/books');
        //Teste de reposta da Rota
        $response->assertStatus(200);
        //Teste de dados retornados pela rota
        $response->assertJsonCount(3);

        //Teste dos dados retornados pelo JSON
        $response->assertJson(function (AssertableJson $json) use($books){
           //Teste dos tipos de dados retornados
           $json->whereAllType([
                '0.id'=> 'integer',
                '1.id'=> 'integer',
                '2.id'=> 'integer',
                '0.title'=> 'string',
                '0.isbn'=> 'string',
            ]);
           //Teste das colunas retornadas
           $json->hasAll(['0.id', '0.title', '0.isbn']);
           //Teste dos dados no banco de dados
           $book = $books->first();
            $json->whereAll([
                '0.id'=> $book->id,
                '0.title'=> $book->title,
                '0.isbn'=> $book->isbn,
            ]);
        });

    }

    public function test_get_single_book_endpoint (): void
    {
        $book = Book::factory(1)->createOne();

        //Teste de rota
        $response = $this->getJson('/api/books/'.$book->id);
        //Teste de reposta da Rota
        $response->assertStatus(200);

        //Teste dos dados retornados pelo JSON
        $response->assertJson(function (AssertableJson $json) use($book){

            //Teste das colunas retornadas
            $json->hasAll(['id', 'title', 'isbn'])->etc();

            //Teste dos tipos de dados retornados
            $json->whereAllType([
                'id'=> 'integer',
                'title'=> 'string',
                'isbn'=> 'string',
            ]);

            //Teste dos dados no banco de dados
            $json->whereAll([
                'id'=> $book->id,
                'title'=> $book->title,
                'isbn'=> $book->isbn,
            ]);
        });

    }

    public function test_post_book_endpoint(){
        //Cria dados em formato de objeto (model)
        $book = Book::factory(1)->makeOne()->toArray();

        //Teste da rota POST API
        $response = $this->postJson('/api/books', $book);

        //Retorna status created
        $response->assertStatus(201);

        $response->assertJson(function (AssertableJson $json) use($book){

            $json->hasAll(['id', 'title', 'isbn'])->etc();
            //Teste as colunas do banco de dados
            $json->whereAll([
                'title'=> $book['title'],
                'isbn'=> $book['isbn'],
            ])->etc();
        });
    }

    public function test_put_book_endpoint(){

        $data = Book::factory(1)->createOne();
        $book = [
            'title'=>'Atualizando Livro',
            'isbn' =>'1234567890',
        ];

        $response = $this->putJson('/api/books/'.$data->id,$book);
        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) use($book){

            $json->hasAll(['id', 'title', 'isbn'])->etc();

            //Teste as colunas do banco de dados
            $json->whereAll([
                'title'=> $book['title'],
                'isbn'=> $book['isbn'],
            ])->etc();
        });
    }

    public function test_patch_book_endpoint(){

        $data = Book::factory(1)->createOne();
        $book = [
            'title'=>'Atualizando Livro Patch',
        ];

        $response = $this->patchJson('/api/books/'.$data->id,$book);
        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) use($book){

            $json->hasAll(['id', 'title', 'isbn'])->etc();

            //Teste as colunas do banco de dados
            $json->where('title', $book['title']);
        });
    }

    public function test_delete_book_endpoint(){
        $data = Book::factory(1)->createOne();
        $response = $this->deleteJson('/api/books/'.$data->id);

        $response->assertStatus(204);
    }
    
}
