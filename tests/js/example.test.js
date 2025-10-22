/**
 * Example JavaScript Test
 * Tests basic JavaScript functionality
 */

/* eslint-env jest */
describe("Example Test Suite", () => {
    test("basic math works", () => {
        expect(2 + 2).toBe(4);
    });

    test("string concatenation works", () => {
        const greeting = "Hello" + " " + "World";
        expect(greeting).toBe("Hello World");
    });

    test("array operations work", () => {
        const arr = [1, 2, 3];
        expect(arr.length).toBe(3);
        expect(arr).toContain(2);
    });
});
