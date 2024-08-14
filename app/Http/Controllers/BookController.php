<?php

namespace App\Http\Controllers;

use App\Http\Requests\Books\StoreBookRequest;
use App\Http\Requests\Books\UpdateBookRequest;
use App\Services\BookService;
use App\Services\ApiResponseService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BookController extends Controller
{
    /**
     * @var BookService
     */
    protected $bookService;

    /**
     * BookController constructor.
     *
     * @param BookService $bookService
     */
    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
        $this->middleware(['auth:api', 'permission:Create-Book'])->only('store');
        $this->middleware(['auth:api', 'permission:Update-Book'])->only('update');
        $this->middleware(['auth:api', 'permission:Delete-Book'])->only('destroy');
    }

    /**
     * List all books with optional filters.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // Extract category and author filters from the request
        $filters = $request->only(['author']);
        $perPage = $request->input('per_page', 15); // Default to 15 if not provided

        // Get the list of books with the specified filters and pagination
        $books = $this->bookService->listBooks($filters, $perPage);

        // Return a paginated response with the list of books
        return ApiResponseService::paginated($books, 'Books retrieved successfully');
    }

    /**
     * Store a new book.
     *
     * @param StoreBookRequest $request
     * @return JsonResponse
     */
    public function store(StoreBookRequest $request): JsonResponse
    {
        // Validate the request data
        $data = $request->validated();

        // Create a new book with the validated data
        $book = $this->bookService->createBook($data);

        // Return a success response with the created book data
        return ApiResponseService::success($book, 'Book created successfully', 201);
    }

    /**
     * Show details of a specific book.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        // Retrieve the details of the book by its ID
        $book = $this->bookService->getBook($id);

        // Return a success response with the book details
        return ApiResponseService::success($book, 'Book details retrieved successfully');
    }

    /**
     * Update a specific book.
     *
     * @param UpdateBookRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateBookRequest $request, int $id): JsonResponse
    {
        // Validate the request data
        $data = $request->validated();

        // Update the book with the validated data
        $book = $this->bookService->updateBook($data, $id);

        // Return a success response with the updated book data
        return ApiResponseService::success($book, 'Book updated successfully');
    }

    /**
     * Delete a specific book.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        // Delete the book by its ID
        $this->bookService->deleteBook($id);

        // Return a success response indicating the book was deleted
        return ApiResponseService::success(null, 'Book deleted successfully');
    }
}
