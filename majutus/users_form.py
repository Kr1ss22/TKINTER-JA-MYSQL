import sqlite3
import tkinter as tk
from tkinter import messagebox

def open_user_form():
    def add_user():
        if not first.get() or not last.get() or not email.get():
            messagebox.showerror("Viga", "Palun täida kõik kohustuslikud väljad!")
            return
        conn = sqlite3.connect('kmustkivi.db')
        cur = conn.cursor()
        cur.execute("INSERT INTO users (firstname, lastname, email, phone) VALUES (?, ?, ?, ?)",
                    (first.get(), last.get(), email.get(), phone.get()))
        conn.commit()
        conn.close()
        messagebox.showinfo("Edu", "Kasutaja lisatud!")
        window.destroy()

    window = tk.Toplevel()
    window.title("Lisa kasutaja")

    tk.Label(window, text="Eesnimi *").pack()
    first = tk.Entry(window)
    first.pack()

    tk.Label(window, text="Perenimi *").pack()
    last = tk.Entry(window)
    last.pack()

    tk.Label(window, text="Email *").pack()
    email = tk.Entry(window)
    email.pack()

    tk.Label(window, text="Telefon").pack()
    phone = tk.Entry(window)
    phone.pack()

    tk.Button(window, text="Lisa", command=add_user).pack(pady=10)
