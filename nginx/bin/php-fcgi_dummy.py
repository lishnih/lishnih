#!/usr/bin/env python
# coding=utf-8
# Stan 2011-07-22

from __future__ import ( division, absolute_import,
                         print_function, unicode_literals )

import sys

py_version = sys.version_info[:2]
PY3 = py_version[0] == 3

if PY3:
    import socketserver as SocketServer
else:
    import SocketServer


class MyTCPHandler(SocketServer.BaseRequestHandler):
    """
    The RequestHandler class for our server.

    It is instantiated once per connection to the server, and must
    override the handle() method to implement communication to the
    client.
    """

    def handle(self):
        # self.request is the TCP socket connected to the client
        self.data = self.request.recv(1024).strip()
        print("{0} wrote:".format(self.client_address[0]))
        print(self.data)
        # just send back the same data, but upper-cased
        self.request.send(self.data)

if __name__ == "__main__":
    HOST, PORT = "localhost", 9000

    # Create the server, binding to localhost on port 9000
    server = SocketServer.TCPServer((HOST, PORT), MyTCPHandler)

    # Activate the server; this will keep running until you
    # interrupt the program with Ctrl-C
    server.serve_forever()
