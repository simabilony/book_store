<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAuthorRequest;
use App\Http\Requests\UpdateAuthorRequest;
use App\Models\Author;
use App\Service\ApiResponseService;
use App\Service\AuthorService;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    protected $authorService;
public function __construct(AuthorService $authorService)
{
        $this->authorService=$authorService;
}

    /**
     * Display a listing of the resource.
     * @throws \Exception
     */
    public function index(Request $request)
    {
        $per_page=$request->only(['per_page']);
        $authors=$this->authorService->listAuthor($per_page);
        return ApiResponseService::paginated($authors);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAuthorRequest $request): \Illuminate\Http\JsonResponse
    {
        // Validate the request data
        $data = $request->validated();

        // Create a new author with the validated data
        $author = $this->authorService->createAuthor($data);

        // Return a success response with the created author data
        return ApiResponseService::success($author, 'Author created successfully', 201);
    }
    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        // Retrieve the details of the author by its ID
        $author = $this->authorService->getAuthor($id);

        // Return a success response with the author details
        return ApiResponseService::success($author, 'Author details retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAuthorRequest $request, int $id)
    {
        // Validate the request data
        $data = $request->validated();

        // Update the author with the validated data
        $author = $this->authorService->updateAuthor($data, $id);

        // Return a success response with the updated author data
        return ApiResponseService::success($author, 'Author updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        // Delete the author by its ID
        $this->authorService->deleteAuthor($id);

        // Return a success response indicating the author was deleted
        return ApiResponseService::success(null, 'Author deleted successfully');
    }
}
