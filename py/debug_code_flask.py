#!/usr/bin/env python
# coding=utf-8

from __future__ import ( division, absolute_import,
                         print_function, unicode_literals )

import logging

from flask import Flask
app = Flask(__name__, static_url_path='')


@app.route("/")
def index():

### Input your code here ###


    k
    try:
        raise Exception("User exception")
    except Exception as e:
        message = "Error: {0!r}".format(e)
        print(message)


############################

    return "The code performed successfully!"


if __name__ == '__main__':
    logging.basicConfig(level=logging.INFO)

    app.run(debug=True)
