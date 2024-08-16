<?php

namespace App\Service;

use App\Models\Book;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookService
{
    /**
     * List all books with optional filters.
     *
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listBooks(array $filters, int $perPage): LengthAwarePaginator
    {
        // Generate a unique cache key based on filters and pagination
        $cacheKey = 'books_' . md5(json_encode($filters) . $perPage . request('page', 1));

        // Check if the cached result exists
        $books = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($filters, $perPage) {
            // Initialize the query builder for the Book model
            $booksQuery = Book::query();

            // Apply author filter if provided
            if (isset($filters['author'])) {
                $booksQuery->where('author_id', $filters['author']);
            }

            $booksQuery->select(['name', 'image', 'author']);

            // Return the paginated result of the query
            return $booksQuery->paginate($perPage);
        });

        return $books;
    }

    public function createBook(array $data)
    {
        DB::beginTransaction();

        try {
            // Check if an image file is present in the data
            if (isset($data['image'])) {
                $file = $data['image'];
                $originalName = $file->getClientOriginalName();

                // Check for double extensions in the file name
                if (preg_match('/\.[^.]+\./', $originalName)) {
                    throw new Exception(trans('general.notAllowedAction'), 403);
                }

                // Validate the MIME type and extension
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $allowedExtensions = ['jpeg', 'jpg', 'png', 'gif'];
                $mime_type = $file->getClientMimeType();
                $extension = $file->getClientOriginalExtension();

                if (!in_array($mime_type, $allowedMimeTypes) || !in_array($extension, $allowedExtensions)) {
                    throw new Exception(trans('general.invalidFileType'), 403);
                }

                // Sanitize the file name to prevent path traversal
                $fileName = Str::random(32);
                $fileName = preg_replace('/[^A-Za-z0-9_\-]/', '', $fileName);

                // Store the file in the 'public' disk
                $path = $file->storeAs('images', $fileName . '.' . $extension, 'public');

                // Verify the path to ensure it matches the expected pattern
                $expectedPath = storage_path('app/public/images/' . $fileName . '.' . $extension);
                if (realpath(storage_path('app/public') . '/' . $path) !== $expectedPath) {
                    Storage::disk('public')->delete($path);
                    throw new Exception(trans('general.notAllowedAction'), 403);
                }

                // Get the URL of the stored file
                $url = Storage::disk('public')->url($path);

                // Add the image URL to the data array
                $data['image_url'] = $url;

                // Remove the image file from the data array
                unset($data['image']);
            }

            // Create a new book record with the provided data
            $book = Book::create($data);

            // Commit the transaction
            DB::commit();

            return $book;
        } catch (Exception $e) {
            // Rollback the transaction on failure
            DB::rollBack();

            // Delete the file if it was stored
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }

            throw $e;
        }
    }



    /**
     * Get the details of a specific book by its ID.
     *
     * @param int $id
     * @return \App\Models\Book
     */
    public function getBook(int $id)
    {
        // Find the book by ID or fail with a 404 error if not found
        return Book::findOrFail($id);
    }

    /**
     * Update the details of a specific book.
     *
     * @param array $data
     * @param int $id
     * @return \App\Models\Book
     */
    public function updateBook(array $data, int $id)
    {
        // Find the book by ID or fail with a 404 error if not found
        $book = Book::findOrFail($id);

        // Update the book with the provided data, filtering out null values
        $book->update(array_filter($data));

        // Return the updated book
        return $book;
    }

    /**
     * Delete a specific book by its ID.
     *
     * @param int $id
     * @return void
     */
    public function deleteBook(int $id)
    {
        // Find the book by ID or fail with a 404 error if not found
        $book = Book::findOrFail($id);

        // Delete the book
        $book->delete();
    }
}
