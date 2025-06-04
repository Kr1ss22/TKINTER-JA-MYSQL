import sqlite3
import tkinter as tk
from tkinter import messagebox, ttk

DB_NAME = 'kmustkivi.db'  # Su andmebaas

def load_users(search_query=""):
    for row in tree.get_children():
        tree.delete(row)

    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    if search_query:
        c.execute("SELECT * FROM users WHERE firstname LIKE ?", ('%' + search_query + '%',))
    else:
        c.execute("SELECT * FROM users")
    for row in c.fetchall():
        tree.insert("", "end", values=row)
    conn.close()

def open_user_form():
    form = tk.Toplevel(root)
    form.title("Lisa uus kasutaja")

    tk.Label(form, text="Eesnimi").grid(row=0, column=0)
    tk.Label(form, text="Perenimi").grid(row=1, column=0)
    tk.Label(form, text="Email").grid(row=2, column=0)
    tk.Label(form, text="Telefon").grid(row=3, column=0)
    tk.Label(form, text="Profiilipilt (URL)").grid(row=4, column=0)

    firstname_entry = tk.Entry(form)
    lastname_entry = tk.Entry(form)
    email_entry = tk.Entry(form)
    phone_entry = tk.Entry(form)
    picture_entry = tk.Entry(form)

    firstname_entry.grid(row=0, column=1)
    lastname_entry.grid(row=1, column=1)
    email_entry.grid(row=2, column=1)
    phone_entry.grid(row=3, column=1)
    picture_entry.grid(row=4, column=1)

    def save_user():
        firstname = firstname_entry.get()
        lastname = lastname_entry.get()
        email = email_entry.get()
        phone = phone_entry.get()
        picture = picture_entry.get()

        if not firstname or not lastname or not email:
            messagebox.showerror("Viga", "Palun täida kõik kohustuslikud väljad!")
            return

        try:
            conn = sqlite3.connect(DB_NAME)
            c = conn.cursor()
            c.execute("INSERT INTO users (firstname, lastname, email, phone, profile_picture) VALUES (?, ?, ?, ?, ?)",
                      (firstname, lastname, email, phone, picture))
            conn.commit()
            conn.close()
            load_users()
            form.destroy()
            messagebox.showinfo("Edu", "Kasutaja lisati edukalt.")
        except Exception as e:
            messagebox.showerror("Viga", str(e))

    tk.Button(form, text="Salvesta", command=save_user).grid(row=5, columnspan=2, pady=10)

def edit_selected_user():
    selected = tree.focus()
    if not selected:
        messagebox.showwarning("Teade", "Palun vali kasutaja, keda muuta.")
        return

    values = tree.item(selected, "values")
    user_id = values[0]

    form = tk.Toplevel(root)
    form.title("Muuda kasutajat")

    tk.Label(form, text="Eesnimi").grid(row=0, column=0)
    tk.Label(form, text="Perenimi").grid(row=1, column=0)
    tk.Label(form, text="Email").grid(row=2, column=0)
    tk.Label(form, text="Telefon").grid(row=3, column=0)
    tk.Label(form, text="Profiilipilt (URL)").grid(row=4, column=0)

    firstname_entry = tk.Entry(form)
    lastname_entry = tk.Entry(form)
    email_entry = tk.Entry(form)
    phone_entry = tk.Entry(form)
    picture_entry = tk.Entry(form)

    firstname_entry.grid(row=0, column=1)
    lastname_entry.grid(row=1, column=1)
    email_entry.grid(row=2, column=1)
    phone_entry.grid(row=3, column=1)
    picture_entry.grid(row=4, column=1)

    firstname_entry.insert(0, values[1])
    lastname_entry.insert(0, values[2])
    email_entry.insert(0, values[3])
    phone_entry.insert(0, values[4])
    picture_entry.insert(0, values[5])

    def update_user():
        firstname = firstname_entry.get()
        lastname = lastname_entry.get()
        email = email_entry.get()
        phone = phone_entry.get()
        picture = picture_entry.get()

        try:
            conn = sqlite3.connect(DB_NAME)
            c = conn.cursor()
            c.execute("""
                UPDATE users
                SET firstname = ?, lastname = ?, email = ?, phone = ?, profile_picture = ?
                WHERE id = ?
            """, (firstname, lastname, email, phone, picture, user_id))
            conn.commit()
            conn.close()
            load_users()
            form.destroy()
            messagebox.showinfo("Edu", "Kasutaja andmed uuendatud.")
        except Exception as e:
            messagebox.showerror("Viga", str(e))

    tk.Button(form, text="Salvesta muudatused", command=update_user).grid(row=5, columnspan=2, pady=10)

# Põhiaken
root = tk.Tk()
root.title("Kasutajate haldus")

tk.Label(root, text="Otsi eesnime järgi:").pack()
search_entry = tk.Entry(root)
search_entry.pack()

def on_search(event=None):
    query = search_entry.get()
    load_users(query)

search_entry.bind("<Return>", on_search)

tree = ttk.Treeview(root, columns=("id", "firstname", "lastname", "email", "phone", "picture"), show="headings")
tree.heading("id", text="ID")
tree.heading("firstname", text="Eesnimi")
tree.heading("lastname", text="Perenimi")
tree.heading("email", text="Email")
tree.heading("phone", text="Telefon")
tree.heading("picture", text="Profiilipilt")
tree.pack(expand=True, fill='both')

scrollbar = ttk.Scrollbar(root, orient="vertical", command=tree.yview)
tree.configure(yscrollcommand=scrollbar.set)
scrollbar.pack(side="right", fill="y")

tk.Button(root, text="Lisa uus kasutaja", command=open_user_form).pack(pady=5)
tk.Button(root, text="Muuda valitud kasutajat", command=edit_selected_user).pack(pady=5)

load_users()
root.mainloop()
