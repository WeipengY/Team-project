import math
import sympy as sp
from scipy.misc import derivative
import matplotlib.pyplot as plt
import numpy as np 
b=sp.Symbol('b')
a=sp.Symbol('a')
t=[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18]
k=[0.00758,0.00695,0.00645,0.00601,0.00587,0.00589,0.00528,0.00517,0.00508,
0.00487,0.00479,0.00479,0.00495,0.00492,0.00521,0.00496,0.00586,0.00532,
0.00381]
def f(b, a, t, k):
  s=0
  n=len(t)
  for i in range(n):
    s=s+(k[i]-(b+a*t[1]))**2
    return s

s=f(b,a,t,k)
s
def dfa(s,a):  
  return sp.diff(s,a)  

A=dfa(s,a)
A
def dfb(s,b):
  return sp.diff(s,b)
B=dfa(s,b)
B
sp.linsolve([A,B],(a,b))

pret=list(range(19,31))
def prek(pret):
  prek=[]
  length=len(pret)
  for i in range(length):
    k = -0.000116877192982457*pret[i]+0.00651294736842106
    prek.append(k)
  return prek
prek=prek(pret)
prek
    
p0=1393686493
def prepopulation(pret,prek,p0):
  P=[]
  length=len(pret)
  for i in range(length):
    p=p0*(1+prek[i])
    p0=p
    P.append(p)
  return P
P = prepopulation(pret,prek,p0)
P

for i in range(len(pret)):
  print('In year',2019+i,',the predicted population is', 
        round(P[i]/1000000000,4),'billion.')

plt.plot(pret,P,'-',color="red")
plt.grid(True)    
plt.xlabel('t')
plt.ylabel('Predicted Population')
plt.title("Predicted Population VS Time")
plt.show

plt.plot(pret,prek,'-',color="green",label="Growth Rate VS Time")
plt.grid(True)
plt.xlabel('t')
plt.ylabel('Growth Rate')
plt.title("Growth Rate VS Time")

xx=[15,20,30]
yy=[1375000000,1420000000,1450000000]

def lagrange(x, i, xx):
  n=len(xx)
  l=1.0
  for j in range(n):
    if i!=j:
      l*=(x-xx[j])/(xx[i]-xx[j])
  return l

def interpolation(x,xx,yy):
  n = len(xx)
  y = 0
  for i in range(n):
    y = y+lagrange(x,i,xx)*yy[i]
  return y

def result(xx,yy):
  result=[]
  for i in range(19,31):
    p = interpolation(i,xx,yy)
    result.append(p)
  return result

govp=result(xx,yy)
govp

def rerror(govp,p):
  rerror = []
  n = len(govp)
  for i in range(n):
    r = abs(govp[i]-P[i])/abs(govp[i])
    rerror.append(r)
  return rerror
rerror(govp,P)

plt.plot(pret,P,'-',color="red",label="Predected Population")
plt.plot(pret,govp,'-',color="green",label="Expected Standard Population")
plt.grid(True)
plt.xlabel('t')
plt.ylabel('Population')
plt.title("Population vs Time")
plt.legend()
  
